<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('ingredients')->nullable()->after('description');
            $table->unsignedInteger('calories')->nullable()->after('ingredients');
            $table->string('spice_level')->nullable()->after('calories'); // mild, medium, hot
            $table->unsignedInteger('prep_time_minutes')->nullable()->after('spice_level');
            $table->json('extras')->nullable()->after('prep_time_minutes');
            $table->decimal('tax_rate', 8, 4)->nullable()->after('price'); // null = use store setting
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'ingredients',
                'calories',
                'spice_level',
                'prep_time_minutes',
                'extras',
                'tax_rate',
            ]);
        });
    }
};
