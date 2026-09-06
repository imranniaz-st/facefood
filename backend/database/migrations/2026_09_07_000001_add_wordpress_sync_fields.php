<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('wordpress_term_id')->nullable()->unique()->after('is_active');
            $table->timestamp('synced_at')->nullable()->after('wordpress_term_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('wordpress_product_id')->nullable()->unique()->after('is_available');
            $table->string('woocommerce_sku', 100)->nullable()->unique()->after('wordpress_product_id');
            $table->timestamp('synced_at')->nullable()->after('woocommerce_sku');
        });

        Schema::table('deals', function (Blueprint $table) {
            $table->unsignedBigInteger('wordpress_post_id')->nullable()->unique()->after('is_active');
            $table->timestamp('synced_at')->nullable()->after('wordpress_post_id');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn(['wordpress_post_id', 'synced_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['wordpress_product_id', 'woocommerce_sku', 'synced_at']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['wordpress_term_id', 'synced_at']);
        });
    }
};
