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
        // Change quantity from decimal to integer
        DB::statement('ALTER TABLE stock_movements MODIFY quantity INT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert quantity back to decimal
        DB::statement('ALTER TABLE stock_movements MODIFY quantity DECIMAL(10, 2)');
    }
};
