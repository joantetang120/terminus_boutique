<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // Add product_id FK
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete()->after('invoice_id');

            // Rename designation and keep it (already exists, but ensure it's snapshot)
            // designation already exists, no change needed

            // Change unit enum to unit_sold varchar
            $table->string('unit_sold')->after('designation');
            $table->dropColumn('unit');

            // Rename quantity to quantity_sold
            $table->renameColumn('quantity', 'quantity_sold');

            // Add quantity_deducted (stock deduction after conversion)
            $table->decimal('quantity_deducted', 10, 2)->default(0)->after('quantity_sold');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');

            $table->dropColumn('unit_sold');
            $table->enum('unit', ['carton', 'boite', 'paquet', 'piece'])->after('designation');

            $table->renameColumn('quantity_sold', 'quantity');
            $table->dropColumn('quantity_deducted');
        });
    }
};
