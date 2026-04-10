<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Facturation
            'facture.view',
            'facture.create',
            'facture.edit',
            'facture.cancel',
            // Stock
            'stock.view',
            'stock.create',
            'stock.edit',
            'stock.cancel',
            // Comptabilité
            'compta.view',
            'compta.create',
            'compta.edit',
            'compta.approve',
            // Ghost
            'ghost.view',
            // Users
            'user.view',
            'user.create',
            'user.edit',
            // Audit
            'audit.view',
            // Products
            'product.view',
            'product.create',
            'product.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@boutique.cm'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('Admin@1234'),
                'is_active' => true,
            ]
        );

        // Give admin all permissions
        $admin->givePermissionTo(Permission::all());

        // Create some sample products
        \App\Models\Product::firstOrCreate(
            ['name' => 'Produit A'],
            [
                'description' => 'Description du produit A',
                'unit' => 'carton',
                'current_stock' => 100,
                'alert_threshold' => 10,
                'is_active' => true,
                'created_by' => $admin->id,
            ]
        );

        \App\Models\Product::firstOrCreate(
            ['name' => 'Produit B'],
            [
                'description' => 'Description du produit B',
                'unit' => 'boite',
                'current_stock' => 50,
                'alert_threshold' => 5,
                'is_active' => true,
                'created_by' => $admin->id,
            ]
        );

        \App\Models\Product::firstOrCreate(
            ['name' => 'Produit C'],
            [
                'description' => 'Description du produit C',
                'unit' => 'paquet',
                'current_stock' => 200,
                'alert_threshold' => 20,
                'is_active' => true,
                'created_by' => $admin->id,
            ]
        );

        // Get product IDs for stock movements
        $productA = \App\Models\Product::where('name', 'Produit A')->first();
        $productB = \App\Models\Product::where('name', 'Produit B')->first();
        $productC = \App\Models\Product::where('name', 'Produit C')->first();

        // Create sample stock movements (entries)
        \App\Models\StockMovement::create([
            'product_id' => $productA->id,
            'type' => 'entry',
            'quantity' => 100,
            'reference_type' => null,
            'reference_id' => null,
            'note' => 'Stock initial',
            'created_by' => $admin->id,
        ]);

        \App\Models\StockMovement::create([
            'product_id' => $productB->id,
            'type' => 'entry',
            'quantity' => 50,
            'reference_type' => null,
            'reference_id' => null,
            'note' => 'Stock initial',
            'created_by' => $admin->id,
        ]);

        \App\Models\StockMovement::create([
            'product_id' => $productC->id,
            'type' => 'entry',
            'quantity' => 200,
            'reference_type' => null,
            'reference_id' => null,
            'note' => 'Stock initial',
            'created_by' => $admin->id,
        ]);

        // Create sample stock exit
        \App\Models\StockMovement::create([
            'product_id' => $productA->id,
            'type' => 'exit',
            'quantity' => 20,
            'reference_type' => null,
            'reference_id' => null,
            'note' => 'Vente test',
            'created_by' => $admin->id,
        ]);
    }
}
