<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Tracer l'unité et quantité saisies par l'utilisateur (avant conversion)
            $table->string('input_unit')->nullable()->after('quantity');
            $table->integer('input_quantity')->nullable()->after('input_unit');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn(['input_unit', 'input_quantity']);
        });
    }
};
