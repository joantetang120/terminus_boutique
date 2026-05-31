<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Unité d'achat (fournisseur) et son taux de conversion
            $table->enum('purchase_unit', ['carton', 'boite', 'paquet', 'piece', 'sceau', 'sacs', 'palettes', 'filet', 'bidon'])->nullable()->after('unit');
            $table->integer('purchase_conversion_rate')->nullable()->default(1)->after('purchase_unit');
            
            // Unité de vente alternative et son taux de conversion
            $table->enum('sale_unit', ['carton', 'boite', 'paquet', 'piece', 'sceau', 'sacs', 'palettes', 'filet', 'bidon'])->nullable()->after('purchase_conversion_rate');
            $table->integer('sale_conversion_rate')->nullable()->default(1)->after('sale_unit');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_unit',
                'purchase_conversion_rate',
                'sale_unit',
                'sale_conversion_rate',
            ]);
        });
    }
};
