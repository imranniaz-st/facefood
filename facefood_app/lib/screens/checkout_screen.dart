import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/auth_provider.dart';
import '../providers/cart_provider.dart';
import '../providers/catalog_provider.dart';
import '../providers/settings_provider.dart';
import '../theme/app_theme.dart';
import '../utils/format.dart';
import '../utils/user_error.dart';
import '../widgets/map_placeholder.dart';
import '../widgets/order_summary.dart';

class CheckoutScreen extends StatefulWidget {
  const CheckoutScreen({super.key});

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  String _payment = 'cash_on_delivery';
  final _note = TextEditingController();
  bool _submitting = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (context.read<AuthProvider>().isAuthenticated) {
        context.read<CatalogProvider>().loadAddresses();
      }
      _refreshQuote();
    });
  }

  @override
  void dispose() {
    _note.dispose();
    super.dispose();
  }

  Future<void> _refreshQuote() async {
    final cart = context.read<CartProvider>();
    if (cart.items.isEmpty) return;
    try {
      final quote = await context.read<CatalogProvider>().quote(cart.toOrderPayload());
      if (!mounted) return;
      cart.setServerQuote(quote);
    } catch (_) {}
  }

  Future<void> _placeOrder() async {
    final auth = context.read<AuthProvider>();
    if (!auth.isAuthenticated) {
      Navigator.of(context).pushNamed('/login');
      return;
    }

    final cart = context.read<CartProvider>();
    final catalog = context.read<CatalogProvider>();
    if (cart.items.isEmpty) return;

    setState(() => _submitting = true);
    try {
      final address = catalog.defaultAddress;
      final order = await catalog.placeOrder(
        items: cart.toOrderPayload(),
        paymentMethod: _payment,
        addressId: address?.id,
        deliveryAddress: address?.fullAddress ?? 'PWD, Islamabad',
        note: _note.text.trim(),
      );
      cart.clear();
      if (!mounted) return;
      Navigator.of(context).pushReplacementNamed('/order-confirmation', arguments: order);
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(userFacingError(e, fallback: "Couldn't place your order. Please try again."))),
      );
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<CartProvider>();
    final catalog = context.watch<CatalogProvider>();
    final settings = context.watch<SettingsProvider>().settings;
    final addressText = catalog.defaultAddress?.fullAddress ?? 'PWD, Islamabad';
    final currency = settings.currency;

    return Scaffold(
      appBar: AppBar(title: const Text('Checkout')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          const Text('Delivery Details', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
          const SizedBox(height: 10),
          MapPlaceholder(address: addressText),
          const SizedBox(height: 8),
          Row(
            children: [
              const Icon(Icons.access_time, size: 16, color: AppColors.primary),
              const SizedBox(width: 6),
              Text(
                'EST. ${settings.estimatedDelivery}',
                style: const TextStyle(fontWeight: FontWeight.w600, color: AppColors.primary, fontSize: 13),
              ),
              const Spacer(),
              TextButton(
                onPressed: () async {
                  final auth = context.read<AuthProvider>();
                  if (!auth.isAuthenticated) {
                    Navigator.of(context).pushNamed('/login');
                    return;
                  }
                  await catalog.loadAddresses();
                  if (!context.mounted) return;
                  await Navigator.of(context).pushNamed('/addresses/pick');
                },
                child: const Text('Change'),
              ),
            ],
          ),
          Text(addressText, style: const TextStyle(fontSize: 12, color: AppColors.muted)),
          const SizedBox(height: 20),
          const Text('Payment Method', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
          const SizedBox(height: 8),
          _payTile('cash_on_delivery', 'Cash on Delivery', Icons.payments_outlined),
          _payTile('jazzcash', 'JazzCash', Icons.phone_android),
          _payTile('card', 'Credit / Debit Card', Icons.credit_card),
          const SizedBox(height: 16),
          const Text('Add Note (optional)', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
          const SizedBox(height: 8),
          TextField(
            controller: _note,
            maxLines: 3,
            decoration: const InputDecoration(hintText: 'Any delivery instructions?'),
          ),
          const SizedBox(height: 16),
          const Text('Order Summary', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
          const SizedBox(height: 8),
          ...cart.items.map(
            (i) => Padding(
              padding: const EdgeInsets.only(bottom: 8),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('${i.dealTitle ?? i.product.name} ×${i.quantity}'),
                        if (i.extras.isNotEmpty)
                          Text(
                            i.extrasLabel,
                            style: const TextStyle(fontSize: 12, color: AppColors.muted),
                          ),
                      ],
                    ),
                  ),
                  Text(formatMoney(i.lineTotal, currency), style: const TextStyle(fontWeight: FontWeight.w600)),
                ],
              ),
            ),
          ),
          const SizedBox(height: 10),
          OrderSummary(
            subtotal: cart.displaySubtotal(settings),
            deliveryFee: cart.displayDeliveryFee(settings),
            tax: cart.displayTax(settings),
            total: cart.displayTotal(settings),
            settings: settings,
            taxLabel: cart.displayTaxLabel(settings),
            taxPercent: cart.displayTaxPercent(settings),
          ),
          const SizedBox(height: 88),
        ],
      ),
      bottomNavigationBar: SafeArea(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
          child: ElevatedButton(
            onPressed: _submitting || cart.items.isEmpty ? null : _placeOrder,
            child: _submitting
                ? const SizedBox(
                    height: 22,
                    width: 22,
                    child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                  )
                : Text('Place Order — ${formatMoney(cart.displayTotal(settings), currency)}'),
          ),
        ),
      ),
    );
  }

  Widget _payTile(String value, String label, IconData icon) {
    final selected = _payment == value;
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Material(
        color: selected ? AppColors.primarySoft : Colors.white,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(12),
          side: BorderSide(
            color: selected ? AppColors.primary : AppColors.border,
            width: selected ? 1.5 : 1,
          ),
        ),
        child: InkWell(
          borderRadius: BorderRadius.circular(12),
          onTap: () => setState(() => _payment = value),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
            child: Row(
              children: [
                Icon(
                  selected ? Icons.radio_button_checked : Icons.radio_button_off,
                  color: selected ? AppColors.primary : AppColors.muted,
                  size: 22,
                ),
                const SizedBox(width: 12),
                Icon(icon, color: selected ? AppColors.primary : AppColors.muted, size: 20),
                const SizedBox(width: 10),
                Text(label, style: TextStyle(fontWeight: selected ? FontWeight.w600 : FontWeight.w500)),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
