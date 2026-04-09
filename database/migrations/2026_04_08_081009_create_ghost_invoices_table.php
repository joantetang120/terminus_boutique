<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ghost_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('real_invoice_id')->nullable(); // NO FK constraint
            $table->string('number');
            $table->string('type');
            $table->string('status');
            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->decimal('total', 10, 2);
            $table->decimal('advance_amount', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ghost_invoices');
    }
};
