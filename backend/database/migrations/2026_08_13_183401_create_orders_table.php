<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            $table->string('delivery_address');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(100);
            $table->decimal('tax', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('payment_method'); // cash_on_delivery, jazzcash, card
            $table->string('status')->default('pending'); // pending, confirmed, preparing, on_the_way, delivered, cancelled
            $table->string('verification_code', 4);
            $table->text('note')->nullable();
            $table->unsignedInteger('estimated_minutes')->default(40);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
