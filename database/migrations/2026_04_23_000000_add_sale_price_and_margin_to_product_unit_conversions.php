<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_unit_conversions', function (Blueprint $table) {
            $table->decimal('sale_price', 12, 2)->nullable()->after('conversion_rate');
            $table->decimal('sale_margin_percentage', 5, 2)->nullable()->after('sale_price');
            $table->decimal('minimum_price', 12, 2)->nullable()->after('sale_margin_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_unit_conversions', function (Blueprint $table) {
            $table->dropColumn(['sale_price', 'sale_margin_percentage', 'minimum_price']);
        });
    }
};
