<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->enum('type', ['comptant', 'credit', 'avance']);
            $table->enum('status', ['payee', 'credit', 'avance', 'annulee']);
            $table->string('client_name');
            $table->string('client_phone')->nullable();
            $table->decimal('total', 10, 2);
            $table->decimal('advance_amount', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
