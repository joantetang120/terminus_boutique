<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE products MODIFY COLUMN unit ENUM('carton', 'boite', 'paquet', 'piece', 'sceau', 'sacs', 'palettes', 'filet', 'bidon') NOT NULL");
        DB::statement("ALTER TABLE products MODIFY COLUMN purchase_unit ENUM('carton', 'boite', 'paquet', 'piece', 'sceau', 'sacs', 'palettes', 'filet', 'bidon') NULL");
        DB::statement("ALTER TABLE products MODIFY COLUMN sale_unit ENUM('carton', 'boite', 'paquet', 'piece', 'sceau', 'sacs', 'palettes', 'filet', 'bidon') NULL");
        DB::statement("ALTER TABLE product_unit_conversions MODIFY COLUMN unit ENUM('carton', 'boite', 'paquet', 'piece', 'sceau', 'sacs', 'palettes', 'filet', 'bidon') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE products MODIFY COLUMN unit ENUM('carton', 'boite', 'paquet', 'piece', 'sceau', 'sacs', 'palettes', 'filet') NOT NULL");
        DB::statement("ALTER TABLE products MODIFY COLUMN purchase_unit ENUM('carton', 'boite', 'paquet', 'piece', 'sceau', 'sacs', 'palettes', 'filet') NULL");
        DB::statement("ALTER TABLE products MODIFY COLUMN sale_unit ENUM('carton', 'boite', 'paquet', 'piece', 'sceau', 'sacs', 'palettes', 'filet') NULL");
        DB::statement("ALTER TABLE product_unit_conversions MODIFY COLUMN unit ENUM('carton', 'boite', 'paquet', 'piece', 'sceau', 'sacs', 'palettes', 'filet') NOT NULL");
    }
};
