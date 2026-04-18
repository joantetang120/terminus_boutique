<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_payments', function (Blueprint $table) {
            // Change payment_method from enum to varchar nullable
            $table->string('payment_method')->nullable()->change();

            // Add note field
            $table->text('note')->nullable()->after('payment_method');

            // Add index on invoice_id + payment_date
            $table->index(['invoice_id', 'payment_date'], 'idx_invoice_payments_invoice_date');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->dropIndex('idx_invoice_payments_invoice_date');
            $table->dropColumn('note');
            // Note: Reverting enum change is complex, we'll keep as varchar
        });
    }
};
