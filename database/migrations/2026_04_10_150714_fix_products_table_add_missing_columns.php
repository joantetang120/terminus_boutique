<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add missing columns
            $table->text('description')->nullable()->after('name');
            $table->foreignId('created_by')->nullable()->constrained('users')->after('is_active');
            $table->softDeletes()->after('updated_at');

            // Add index on name for search
            $table->index('name', 'idx_products_name');
        });

        // Fix column types using raw SQL (integer instead of decimal)
        DB::statement('ALTER TABLE products MODIFY current_stock INT DEFAULT 0');
        DB::statement('ALTER TABLE products MODIFY alert_threshold INT DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_name');
            $table->dropForeign(['created_by']);
            $table->dropColumn(['description', 'created_by', 'deleted_at']);
        });

        // Revert column types back to decimal
        DB::statement('ALTER TABLE products MODIFY current_stock DECIMAL(10, 2) DEFAULT 0');
        DB::statement('ALTER TABLE products MODIFY alert_threshold DECIMAL(10, 2) DEFAULT 0');
    }
};
