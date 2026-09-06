<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(private readonly OrderCalculator $calculator) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product', 'address'])
            ->latest()
            ->get();

        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order): OrderResource
    {
        if ($order->user_id !== $request->user()->id) {
            throw ValidationException::withMessages([
                'order' => ['Order not found.'],
            ]);
        }

        $order->load(['items.product', 'address']);

        return new OrderResource($order);
    }

    public function quote(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'items.*.deal_id' => ['nullable', 'integer', 'exists:deals,id'],
            'items.*.extra_ids' => ['nullable', 'array'],
            'items.*.extra_ids.*' => ['integer', 'exists:product_extras,id'],
        ]);

        $products = $this->loadAvailableProducts($data['items']);

        return response()->json([
            'data' => $this->calculator->quote($data['items'], $products),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'address_id' => ['nullable', 'integer', 'exists:addresses,id'],
            'delivery_address' => ['required_without:address_id', 'nullable', 'string', 'max:500'],
            'payment_method' => ['required', Rule::in(Order::PAYMENT_METHODS)],
            'note' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'items.*.deal_id' => ['nullable', 'integer', 'exists:deals,id'],
            'items.*.extra_ids' => ['nullable', 'array'],
            'items.*.extra_ids.*' => ['integer', 'exists:product_extras,id'],
        ]);

        $user = $request->user();
        $address = null;
        $deliveryAddress = $data['delivery_address'] ?? null;

        if (! empty($data['address_id'])) {
            $address = Address::query()
                ->where('user_id', $user->id)
                ->where('id', $data['address_id'])
                ->firstOrFail();
            $deliveryAddress = $address->fullAddress();
        }

        $products = $this->loadAvailableProducts($data['items']);
        $quote = $this->calculator->quote($data['items'], $products);

        $order = DB::transaction(function () use ($data, $user, $address, $deliveryAddress, $quote) {
            $order = Order::create([
                'order_number' => 'FF'.now()->format('ymdHis').random_int(10, 99),
                'user_id' => $user->id,
                'address_id' => $address?->id,
                'delivery_address' => $deliveryAddress,
                'subtotal' => $quote['subtotal'],
                'delivery_fee' => $quote['delivery_fee'],
                'tax' => $quote['tax'],
                'total' => $quote['total'],
                'payment_method' => $data['payment_method'],
                'status' => 'confirmed',
                'verification_code' => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT),
                'note' => $data['note'] ?? null,
                'estimated_minutes' => $quote['estimated_minutes'],
            ]);

            foreach ($quote['items'] as $line) {
                $order->items()->create([
                    'product_id' => $line['product_id'],
                    'deal_id' => $line['deal_id'] ?? null,
                    'product_name' => $line['product_name'],
                    'extras' => $line['extras'] ?? [],
                    'unit_price' => $line['unit_price'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['line_total'],
                ]);
            }

            return $order;
        });

        $order->load(['items.product', 'address']);

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * @param  array<int, array{product_id:int}>  $items
     * @return \Illuminate\Support\Collection<int, Product>
     */
    private function loadAvailableProducts(array $items)
    {
        $productIds = collect($items)->pluck('product_id')->unique();
        $products = Product::query()
            ->whereIn('id', $productIds)
            ->where('is_available', true)
            ->get()
            ->keyBy('id');

        if ($products->count() !== $productIds->count()) {
            throw ValidationException::withMessages([
                'items' => ['One or more products are unavailable.'],
            ]);
        }

        return $products;
    }
}
