<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Facturation
            'facture.view',
            'facture.create',
            'facture.cancel',
            'facture.print',
            'facture.payment',

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
            Permission::firstOrCreate([
                'name' => $permission,
            ]);
        }

        // Create admin user
        $admin = User::firstOrCreate(
            [
                'email' => 'bb9155399@gmail.com',
            ],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('Admin@1234'),
                'is_active' => true,
            ]
        );

        // Give all permissions to admin
        $admin->givePermissionTo(Permission::all());
    }
}