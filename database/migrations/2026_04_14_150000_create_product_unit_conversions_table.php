<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('unit_type', ['purchase', 'sale']); // purchase = achat, sale = vente
            $table->enum('unit', ['carton', 'boite', 'paquet', 'piece', 'sceau', 'sacs', 'palettes']);
            $table->integer('conversion_rate')->default(1); // 1 unit = X base units
            $table->timestamps();

            // Prevent duplicate units per product per type
            $table->unique(['product_id', 'unit_type', 'unit']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_unit_conversions');
    }
};
