<?php

namespace Database\Seeders;

use App\Models\AccountingEntry;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductUnitConversion;
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

        // Create expense categories
        $this->createExpenseCategories();

        // Create realistic products with proper names and units
        $products = $this->createRealisticProducts($admin->id);

        // Create realistic stock movements (entries over past 3 months)
        $this->createRealisticStockMovements($products, $admin->id);

        // Create realistic invoices with various clients and dates
        $this->createRealisticInvoices($products, $admin->id);
    }

    /**
     * Create realistic products commonly found in a boutique
     * Includes base prices, sale prices, and unit conversion prices
     */
    private function createRealisticProducts(int $adminId): array
    {
        $productData = [
            [
                'name' => 'Eau Minérale 1.5L',
                'unit' => 'piece',
                'sale_unit' => 'carton',
                'sale_conversion_rate' => 12,
                'current_stock' => 240,
                'alert_threshold' => 24,
                'base_sale_price' => 500,
                'base_sale_margin_percentage' => 10,
                'conversion_prices' => [
                    ['unit' => 'carton', 'conversion_rate' => 12, 'sale_price' => 5500, 'sale_margin_percentage' => 8],
                ],
            ],
            [
                'name' => 'Jus d\'orange 1L',
                'unit' => 'piece',
                'sale_unit' => 'carton',
                'sale_conversion_rate' => 12,
                'current_stock' => 180,
                'alert_threshold' => 18,
                'base_sale_price' => 750,
                'base_sale_margin_percentage' => 15,
                'conversion_prices' => [
                    ['unit' => 'carton', 'conversion_rate' => 12, 'sale_price' => 8500, 'sale_margin_percentage' => 10],
                ],
            ],
            [
                'name' => 'Coca-Cola 33cl',
                'unit' => 'piece',
                'sale_unit' => 'carton',
                'sale_conversion_rate' => 24,
                'current_stock' => 480,
                'alert_threshold' => 48,
                'base_sale_price' => 400,
                'base_sale_margin_percentage' => 12,
                'conversion_prices' => [
                    ['unit' => 'carton', 'conversion_rate' => 24, 'sale_price' => 9000, 'sale_margin_percentage' => 10],
                ],
            ],
            [
                'name' => 'Chips Nature 100g',
                'unit' => 'paquet',
                'sale_unit' => 'carton',
                'sale_conversion_rate' => 50,
                'current_stock' => 350,
                'alert_threshold' => 35,
                'base_sale_price' => 600,
                'base_sale_margin_percentage' => 20,
                'conversion_prices' => [
                    ['unit' => 'carton', 'conversion_rate' => 50, 'sale_price' => 28000, 'sale_margin_percentage' => 15],
                ],
            ],
            [
                'name' => 'Biscuits Choco',
                'unit' => 'paquet',
                'sale_unit' => 'carton',
                'sale_conversion_rate' => 24,
                'current_stock' => 288,
                'alert_threshold' => 24,
                'base_sale_price' => 550,
                'base_sale_margin_percentage' => 18,
                'conversion_prices' => [
                    ['unit' => 'carton', 'conversion_rate' => 24, 'sale_price' => 12000, 'sale_margin_percentage' => 12],
                ],
            ],
            [
                'name' => 'Pain de mie 500g',
                'unit' => 'paquet',
                'sale_unit' => 'carton',
                'sale_conversion_rate' => 20,
                'current_stock' => 120,
                'alert_threshold' => 12,
                'base_sale_price' => 800,
                'base_sale_margin_percentage' => 10,
                'conversion_prices' => [
                    ['unit' => 'carton', 'conversion_rate' => 20, 'sale_price' => 15000, 'sale_margin_percentage' => 8],
                ],
            ],
            [
                'name' => 'Lait en poudre 400g',
                'unit' => 'boite',
                'sale_unit' => 'carton',
                'sale_conversion_rate' => 12,
                'current_stock' => 144,
                'alert_threshold' => 12,
                'base_sale_price' => 1200,
                'base_sale_margin_percentage' => 15,
                'conversion_prices' => [
                    ['unit' => 'carton', 'conversion_rate' => 12, 'sale_price' => 13500, 'sale_margin_percentage' => 10],
                ],
            ],
            [
                'name' => 'Café soluble 200g',
                'unit' => 'boite',
                'sale_unit' => 'carton',
                'sale_conversion_rate' => 12,
                'current_stock' => 96,
                'alert_threshold' => 10,
                'base_sale_price' => 2500,
                'base_sale_margin_percentage' => 12,
                'conversion_prices' => [
                    ['unit' => 'carton', 'conversion_rate' => 12, 'sale_price' => 28000, 'sale_margin_percentage' => 10],
                ],
            ],
            [
                'name' => 'Sucre en morceaux',
                'unit' => 'boite',
                'sale_unit' => 'carton',
                'sale_conversion_rate' => 10,
                'current_stock' => 150,
                'alert_threshold' => 15,
                'base_sale_price' => 1500,
                'base_sale_margin_percentage' => 10,
                'conversion_prices' => [
                    ['unit' => 'carton', 'conversion_rate' => 10, 'sale_price' => 14000, 'sale_margin_percentage' => 8],
                ],
            ],
            [
                'name' => 'Thé Lipton 25 sachets',
                'unit' => 'boite',
                'sale_unit' => 'carton',
                'sale_conversion_rate' => 24,
                'current_stock' => 192,
                'alert_threshold' => 20,
                'base_sale_price' => 1100,
                'base_sale_margin_percentage' => 15,
                'conversion_prices' => [
                    ['unit' => 'carton', 'conversion_rate' => 24, 'sale_price' => 25000, 'sale_margin_percentage' => 12],
                ],
            ],
        ];

        $products = [];

        foreach ($productData as $data) {
            $conversionPrices = $data['conversion_prices'] ?? [];
            unset($data['conversion_prices']);

            $product = Product::firstOrCreate(
                ['name' => $data['name']],
                array_merge($data, [
                    'description' => 'Produit de consommation courante',
                    'is_active' => true,
                    'created_by' => $adminId,
                ])
            );

            // Create unit conversion prices if product exists and conversions provided
            if ($product->wasRecentlyCreated || $product->exists) {
                foreach ($conversionPrices as $conversion) {
                    ProductUnitConversion::firstOrCreate(
                        [
                            'product_id' => $product->id,
                            'unit' => $conversion['unit'],
                            'unit_type' => 'sale',
                        ],
                        [
                            'conversion_rate' => $conversion['conversion_rate'],
                            'sale_price' => $conversion['sale_price'],
                            'sale_margin_percentage' => $conversion['sale_margin_percentage'],
                            'minimum_price' => $conversion['sale_price'] * (1 - $conversion['sale_margin_percentage'] / 100),
                        ]
                    );
                }
            }

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
            $initialStock = (int) ($product->current_stock * 0.6);
            $purchasePrice = $product->purchase_price ?? 0;
            $initialMovement = StockMovement::create([
                'product_id' => $product->id,
                'type' => 'entry',
                'quantity' => $initialStock,
                'reference_type' => null,
                'reference_id' => null,
                'unit_cost' => $purchasePrice,
                'total_cost' => $purchasePrice * $initialStock,
                'note' => 'Stock initial approvisionnement',
                'created_by' => $adminId,
                'created_at' => $startDate->copy()->addDays(rand(0, 7)),
            ]);

            // Create accounting entry for initial stock if there's a cost
            if ($purchasePrice > 0) {
                AccountingEntry::create([
                    'date' => $startDate->copy()->addDays(rand(0, 7)),
                    'type' => 'depense',
                    'amount' => $purchasePrice * $initialStock,
                    'reference_type' => StockMovement::class,
                    'reference_id' => $initialMovement->id,
                    'description' => 'Stock initial: ' . $initialStock . ' ' . $product->unit . ' de ' . $product->name . ' @ ' . number_format($purchasePrice, 0) . ' FCFA/' . $product->unit,
                    'status' => 'active',
                    'created_by' => $adminId,
                ]);
            }

            // Restocking entries
            for ($i = 0; $i < 3; $i++) {
                $restockQuantity = (int) ($product->current_stock * 0.2);
                $restockMovement = StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'entry',
                    'quantity' => $restockQuantity,
                    'reference_type' => null,
                    'reference_id' => null,
                    'unit_cost' => $purchasePrice,
                    'total_cost' => $purchasePrice * $restockQuantity,
                    'note' => 'Réapprovisionnement ' . ($i + 1),
                    'created_by' => $adminId,
                    'created_at' => $startDate->copy()->addDays(rand(30, 80)),
                ]);

                // Create accounting entry for restocking if there's a cost
                if ($purchasePrice > 0) {
                    AccountingEntry::create([
                        'date' => $startDate->copy()->addDays(rand(30, 80)),
                        'type' => 'depense',
                        'amount' => $purchasePrice * $restockQuantity,
                        'reference_type' => StockMovement::class,
                        'reference_id' => $restockMovement->id,
                        'description' => 'Réapprovisionnement: ' . $restockQuantity . ' ' . $product->unit . ' de ' . $product->name . ' @ ' . number_format($purchasePrice, 0) . ' FCFA/' . $product->unit,
                        'status' => 'active',
                        'created_by' => $adminId,
                    ]);
                }
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

                    // Determine unit to sell (base unit or sale unit)
                    $unitSold = $product->sale_unit ?? $product->unit;
                    $conversionRate = ($unitSold === $product->unit) ? 1 : ($product->sale_conversion_rate ?? 1);

                    // Get price for the selected unit
                    $unitPrice = $this->getUnitPriceForProduct($product, $unitSold);

                    // Add small random variation to price (0-10% above minimum)
                    $marginPercentage = $this->getMarginPercentageForUnit($product, $unitSold);
                    $minPrice = $unitPrice * (1 - $marginPercentage / 100);
                    $actualPrice = $minPrice + rand(0, (int)($unitPrice - $minPrice));
                    $actualPrice = max($minPrice, min($actualPrice, $unitPrice * 1.2)); // Cap at 120% of sale price

                    $itemTotal = $qty * $actualPrice;
                    $total += $itemTotal;

                    $items[] = [
                        'product_id' => $product->id,
                        'designation' => $product->name,
                        'unit_sold' => $unitSold,
                        'quantity_sold' => $qty,
                        'quantity_deducted' => $qty * $conversionRate,
                        'unit_price' => $actualPrice,
                        'original_price' => $actualPrice,
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
                            'unit_cost' => 0,
                            'total_cost' => 0,
                            'note' => 'Vente facture ' . $invoice->number,
                            'created_by' => $adminId,
                            'created_at' => $date,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Get the sale price for a specific unit (base unit or carton)
     */
    private function getUnitPriceForProduct(Product $product, string $unitSold): float
    {
        // Base unit price
        if ($unitSold === $product->unit) {
            return $product->base_sale_price ?? rand(500, 2000);
        }

        // Get conversion price
        $conversion = $product->saleConversions()
            ->where('unit', $unitSold)
            ->whereNotNull('sale_price')
            ->first();

        if ($conversion) {
            return $conversion->sale_price;
        }

        // Fallback: calculate from base price
        if ($product->base_sale_price && $product->sale_conversion_rate) {
            return $product->base_sale_price * $product->sale_conversion_rate;
        }

        return rand(5000, 30000); // Fallback random price
    }

    /**
     * Get the margin percentage for a specific unit
     */
    private function getMarginPercentageForUnit(Product $product, string $unitSold): float
    {
        // Base unit margin
        if ($unitSold === $product->unit) {
            return $product->base_sale_margin_percentage ?? 10;
        }

        // Get conversion margin
        $conversion = $product->saleConversions()
            ->where('unit', $unitSold)
            ->whereNotNull('sale_margin_percentage')
            ->first();

        if ($conversion) {
            return $conversion->sale_margin_percentage;
        }

        return 10; // Default margin
    }

    /**
     * Create predefined expense categories
     */
    private function createExpenseCategories(): void
    {
        $categories = [
            'Loyer',
            'Salaires',
            'Électricité',
            'Eau',
            'Transport',
            'Télécommunications',
            'Fournitures de bureau',
            'Maintenance',
            'Marketing',
            'Assurances',
            'Impôts',
            'Frais bancaires',
            'Repas',
            'Personnel',
            'Formation',
            'Divers',
        ];

        foreach ($categories as $category) {
            \App\Models\ExpenseCategory::firstOrCreate(
                ['name' => $category],
                ['is_active' => true]
            );
        }
    }
}
