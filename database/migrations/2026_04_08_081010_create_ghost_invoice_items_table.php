<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ghost_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ghost_invoice_id')->constrained('ghost_invoices')->cascadeOnDelete();
            $table->string('designation');
            $table->string('unit');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ghost_invoice_items');
    }
};
