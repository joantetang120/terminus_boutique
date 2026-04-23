<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update ghost_invoices to match unified invoices model
        Schema::table('ghost_invoices', function (Blueprint $table) {
            // Remove old fields
            $table->dropColumn('type');
            $table->renameColumn('advance_amount', 'paid_amount');

            // Add new fields
            $table->date('due_date')->nullable()->after('balance');
            $table->timestamp('cancelled_at')->nullable()->after('due_date');
            $table->text('cancel_reason')->nullable()->after('cancelled_at');
        });

        // Update ghost_invoice_items to match unified invoice_items model
        Schema::table('ghost_invoice_items', function (Blueprint $table) {
            // Add product_id
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete()->after('ghost_invoice_id');

            // Change unit to unit_sold (varchar)
            $table->string('unit_sold')->after('designation');
            $table->dropColumn('unit');

            // Rename quantity to quantity_sold
            $table->renameColumn('quantity', 'quantity_sold');

            // Add quantity_deducted
            $table->decimal('quantity_deducted', 10, 2)->default(0)->after('quantity_sold');

            // Add original_price
            $table->decimal('original_price', 10, 2)->after('unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('ghost_invoices', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
            $table->dropColumn('due_date');
            $table->dropColumn('cancelled_at');
            $table->dropColumn('cancel_reason');
            $table->string('type')->after('number');
            $table->decimal('advance_amount', 10, 2)->default(0)->after('total');
        });

        Schema::table('ghost_invoice_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
            $table->dropColumn('unit_sold');
            $table->dropColumn('quantity_deducted');
            $table->dropColumn('original_price');
            $table->string('unit')->after('designation');
            $table->renameColumn('quantity_sold', 'quantity');
        });
    }
};
