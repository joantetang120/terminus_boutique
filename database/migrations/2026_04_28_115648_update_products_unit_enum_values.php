<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update unit column ENUM to include all valid values
        DB::statement("ALTER TABLE products MODIFY COLUMN unit ENUM('carton', 'boite', 'paquet', 'piece', 'sceau', 'sacs', 'palettes', 'filet', 'bidon') NOT NULL");

        // Update purchase_unit column ENUM to include all valid values
        DB::statement("ALTER TABLE products MODIFY COLUMN purchase_unit ENUM('carton', 'boite', 'paquet', 'piece', 'sceau', 'sacs', 'palettes', 'filet', 'bidon') NULL");
        DB::statement("ALTER TABLE products MODIFY COLUMN sale_unit ENUM('carton', 'boite', 'paquet', 'piece', 'sceau', 'sacs', 'palettes', 'filet', 'bidon') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM values
        DB::statement("ALTER TABLE products MODIFY COLUMN unit ENUM('carton', 'boite', 'paquet', 'piece') NOT NULL");
        DB::statement("ALTER TABLE products MODIFY COLUMN purchase_unit ENUM('carton', 'boite', 'paquet', 'piece') NULL");
        DB::statement("ALTER TABLE products MODIFY COLUMN sale_unit ENUM('carton', 'boite', 'paquet', 'piece') NULL");
    }
};
