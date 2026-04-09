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
                'unit' => 'carton',
                'current_stock' => 100,
                'alert_threshold' => 10,
                'is_active' => true,
            ]
        );

        \App\Models\Product::firstOrCreate(
            ['name' => 'Produit B'],
            [
                'unit' => 'boite',
                'current_stock' => 50,
                'alert_threshold' => 5,
                'is_active' => true,
            ]
        );

        \App\Models\Product::firstOrCreate(
            ['name' => 'Produit C'],
            [
                'unit' => 'paquet',
                'current_stock' => 200,
                'alert_threshold' => 20,
                'is_active' => true,
            ]
        );
    }
}
