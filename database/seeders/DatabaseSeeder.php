<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
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
            'facture.view', 'facture.create', 'facture.cancel', 'facture.print', 'facture.payment',
            // Stock
            'stock.view', 'stock.create', 'stock.edit', 'stock.cancel',
            // Comptabilité
            'compta.view', 'compta.create', 'compta.edit', 'compta.approve',
            // Ghost
            'ghost.view',
            // Users
            'user.view', 'user.create', 'user.edit',
            // Audit
            'audit.view',
            // Products
            'product.view', 'product.create', 'product.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'juanjerry120@gmail.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('Admin@1234'),
                'is_active' => true,
            ]
        );
        $admin->givePermissionTo(Permission::all());

        // Create realistic products with proper names and units
        $products = $this->createRealisticProducts($admin->id);

        // Create realistic stock movements (entries over past 3 months)
        $this->createRealisticStockMovements($products, $admin->id);

        // Create realistic invoices with various clients and dates
        $this->createRealisticInvoices($products, $admin->id);
    }

    /**
     * Create realistic products commonly found in a boutique
     */
    private function createRealisticProducts(int $adminId): array
    {
        // sale_unit is enum: ['carton', 'boite', 'paquet', 'piece']
        $productData = [
            [
                'name' => 'Eau Minérale 1.5L',
                'unit' => 'carton',
                'sale_unit' => 'piece',  // bouteille = piece
                'sale_conversion_rate' => 12,
                'current_stock' => 240,
                'alert_threshold' => 24,
            ],
            [
                'name' => 'Jus d\'orange 1L',
                'unit' => 'carton',
                'sale_unit' => 'piece',  // bouteille = piece
                'sale_conversion_rate' => 12,
                'current_stock' => 180,
                'alert_threshold' => 18,
            ],
            [
                'name' => 'Coca-Cola 33cl',
                'unit' => 'carton',
                'sale_unit' => 'piece',  // canette = piece
                'sale_conversion_rate' => 24,
                'current_stock' => 480,
                'alert_threshold' => 48,
            ],
            [
                'name' => 'Chips Nature 100g',
                'unit' => 'carton',
                'sale_unit' => 'paquet',  // sachet = paquet
                'sale_conversion_rate' => 50,
                'current_stock' => 350,
                'alert_threshold' => 35,
            ],
            [
                'name' => 'Biscuits Choco',
                'unit' => 'carton',
                'sale_unit' => 'paquet',
                'sale_conversion_rate' => 24,
                'current_stock' => 288,
                'alert_threshold' => 24,
            ],
            [
                'name' => 'Pain de mie 500g',
                'unit' => 'carton',
                'sale_unit' => 'paquet',
                'sale_conversion_rate' => 20,
                'current_stock' => 120,
                'alert_threshold' => 12,
            ],
            [
                'name' => 'Lait en poudre 400g',
                'unit' => 'carton',
                'sale_unit' => 'boite',
                'sale_conversion_rate' => 12,
                'current_stock' => 144,
                'alert_threshold' => 12,
            ],
            [
                'name' => 'Café soluble 200g',
                'unit' => 'carton',
                'sale_unit' => 'boite',  // pot = boite
                'sale_conversion_rate' => 12,
                'current_stock' => 96,
                'alert_threshold' => 10,
            ],
            [
                'name' => 'Sucre en morceaux',
                'unit' => 'carton',
                'sale_unit' => 'boite',
                'sale_conversion_rate' => 10,
                'current_stock' => 150,
                'alert_threshold' => 15,
            ],
            [
                'name' => 'Thé Lipton 25 sachets',
                'unit' => 'carton',
                'sale_unit' => 'boite',
                'sale_conversion_rate' => 24,
                'current_stock' => 192,
                'alert_threshold' => 20,
            ],
        ];

        $products = [];
        foreach ($productData as $data) {
            $product = Product::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, [
                    'description' => 'Produit de consommation courante',
                    'is_active' => true,
                    'created_by' => $adminId,
                ])
            );
            $products[] = $product;
        }

        return $products;
    }

    /**
     * Create realistic stock movements (entries and exits)
     */
    private function createRealisticStockMovements(array $products, int $adminId): void
    {
        // Create initial stock entries over past 3 months
        $startDate = Carbon::now()->subMonths(3);

        foreach ($products as $product) {
            // Initial stock entry
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'entry',
                'quantity' => (int) ($product->current_stock * 0.6),
                'reference_type' => null,
                'reference_id' => null,
                'note' => 'Stock initial approvisionnement',
                'created_by' => $adminId,
                'created_at' => $startDate->copy()->addDays(rand(0, 7)),
            ]);

            // Restocking entries
            for ($i = 0; $i < 3; $i++) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'entry',
                    'quantity' => (int) ($product->current_stock * 0.2),
                    'reference_type' => null,
                    'reference_id' => null,
                    'note' => 'Réapprovisionnement ' . ($i + 1),
                    'created_by' => $adminId,
                    'created_at' => $startDate->copy()->addDays(rand(30, 80)),
                ]);
            }
        }
    }

    /**
     * Create realistic invoices with various scenarios
     */
    private function createRealisticInvoices(array $products, int $adminId): void
    {
        $clients = [
            ['name' => 'Marie Ngono', 'phone' => '690123456'],
            ['name' => 'Jean Mouelle', 'phone' => '677890123'],
            ['name' => 'Alice Kotto', 'phone' => '699456789'],
            ['name' => 'Paul Essomba', 'phone' => '666234567'],
            ['name' => 'Sophie Biya', 'phone' => '678901234'],
            ['name' => 'Charles Etoga', 'phone' => '655678901'],
            ['name' => 'Fleur Nkoto', 'phone' => '691234567'],
            ['name' => 'Brice Mba', 'phone' => '688345678'],
        ];

        // Invoice scenarios: paid, unpaid, partial, cancelled
        $scenarios = [
            // Paid invoices (60%)
            ['status' => 'SOLDEE', 'paid_percent' => 100, 'count' => 30],
            // Partially paid (20%)
            ['status' => 'PARTIELLE', 'paid_percent' => 50, 'count' => 10],
            // Unpaid (15%)
            ['status' => 'IMPAYEE', 'paid_percent' => 0, 'count' => 8],
            // Cancelled (5%)
            ['status' => 'ANNULEE', 'paid_percent' => 0, 'count' => 3],
        ];

        $invoiceNumber = 1;

        foreach ($scenarios as $scenario) {
            for ($i = 0; $i < $scenario['count']; $i++) {
                $client = $clients[array_rand($clients)];
                $date = Carbon::now()->subDays(rand(0, 60));
                $itemCount = rand(2, 5);

                // Generate items for this invoice
                $items = [];
                $total = 0;

                for ($j = 0; $j < $itemCount; $j++) {
                    $product = $products[array_rand($products)];
                    $qty = rand(1, 5);
                    $unitPrice = rand(500, 5000);
                    $itemTotal = $qty * $unitPrice;
                    $total += $itemTotal;

                    $items[] = [
                        'product_id' => $product->id,
                        'designation' => $product->name,
                        'unit_sold' => $product->sale_unit ?? $product->unit,
                        'quantity_sold' => $qty,
                        'quantity_deducted' => $qty * ($product->sale_conversion_rate ?? 1),
                        'unit_price' => $unitPrice,
                        'original_price' => $unitPrice,
                        'total_price' => $itemTotal,
                    ];
                }

                $paidAmount = (int) ($total * $scenario['paid_percent'] / 100);
                $balance = $total - $paidAmount;

                // Create invoice
                $invoice = Invoice::create([
                    'number' => 'FAC-' . $date->format('Ymd') . '-' . str_pad($invoiceNumber++, 3, '0', STR_PAD_LEFT),
                    'status' => $scenario['status'],
                    'client_name' => $client['name'],
                    'client_phone' => $client['phone'],
                    'total' => $total,
                    'paid_amount' => $paidAmount,
                    'balance' => $balance,
                    'due_date' => $date->copy()->addDays(10)->format('Y-m-d'),
                    'created_by' => $adminId,
                    'created_at' => $date,
                    'cancelled_at' => $scenario['status'] === 'ANNULEE' ? $date->copy()->addDays(2) : null,
                    'cancelled_by' => $scenario['status'] === 'ANNULEE' ? $adminId : null,
                    'cancel_reason' => $scenario['status'] === 'ANNULEE' ? 'Erreur de saisie - facture annulée' : null,
                ]);

                // Create invoice items
                foreach ($items as $item) {
                    InvoiceItem::create(array_merge($item, ['invoice_id' => $invoice->id]));
                }

                // Create stock exits for non-cancelled invoices
                if ($scenario['status'] !== 'ANNULEE') {
                    foreach ($items as $item) {
                        StockMovement::create([
                            'product_id' => $item['product_id'],
                            'type' => 'exit',
                            'quantity' => $item['quantity_deducted'],
                            'reference_type' => Invoice::class,
                            'reference_id' => $invoice->id,
                            'note' => 'Vente facture ' . $invoice->number,
                            'created_by' => $adminId,
                            'created_at' => $date,
                        ]);
                    }
                }
            }
        }
    }
}
