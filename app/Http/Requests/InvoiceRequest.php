<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class InvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('facture.create');
    }

    public function rules(): array
    {
        return [
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:50',
            'total' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.designation' => 'required|string|max:255',
            'items.*.unit_sold' => 'required|string|max:50',
            'items.*.quantity_sold' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.original_price' => 'nullable|numeric|min:0',
            'items.*.conversion_rate' => 'nullable|numeric|min:1',
            'items.*.total_price' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'Le nom du client est obligatoire.',
            'items.required' => 'Au moins un article est requis.',
            'items.*.product_id.required' => 'Le produit est obligatoire.',
            'items.*.product_id.exists' => 'Le produit sélectionné n\'existe pas.',
            'items.*.quantity_sold.min' => 'La quantité doit être supérieure à 0.',
            'items.*.unit_price.min' => 'Le prix unitaire doit être positif.',
        ];
    }

    /**
     * Configure the validator instance.
     * Add custom validation rules for unit_sold and stock sufficiency.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $items = $this->input('items', []);

            foreach ($items as $index => $item) {
                $productId = $item['product_id'] ?? null;
                $unitSold = $item['unit_sold'] ?? null;
                $quantitySold = (float) ($item['quantity_sold'] ?? 0);
                $conversionRate = (float) ($item['conversion_rate'] ?? 1);

                if (!$productId) {
                    continue;
                }

                $product = Product::find($productId);
                if (!$product) {
                    continue;
                }

                // (1) Validate unit_sold is valid (must be unit_sale or unit_purchase of the product)
                $validUnits = $this->getValidUnitsForProduct($product);
                if ($unitSold && !in_array($unitSold, $validUnits)) {
                    $validator->errors()->add(
                        "items.{$index}.unit_sold",
                        "L'unité '{$unitSold}' n'est pas valide pour le produit '{$product->name}'. Unités valides: " . implode(', ', $validUnits)
                    );
                }

                // (2) Calculate quantity_deducted and check stock sufficiency
                $quantityDeducted = $this->calculateQuantityDeducted($product, $unitSold, $quantitySold, $conversionRate);

                if ($quantityDeducted > $product->current_stock) {
                    $validator->errors()->add(
                        "items.{$index}.quantity_sold",
                        "Stock insuffisant pour {$product->name} : {$product->current_stock} disponibles, {$quantityDeducted} demandés"
                    );
                }
            }
        });
    }

    /**
     * Get valid units for a product (base unit, purchase_unit, sale_unit, and unit conversions)
     */
    private function getValidUnitsForProduct(Product $product): array
    {
        $units = [$product->unit];

        if ($product->purchase_unit) {
            $units[] = $product->purchase_unit;
        }

        if ($product->sale_unit && !in_array($product->sale_unit, $units)) {
            $units[] = $product->sale_unit;
        }

        // Add units from unit_conversions table
        foreach ($product->unitConversions as $conversion) {
            if (!in_array($conversion->unit, $units)) {
                $units[] = $conversion->unit;
            }
        }

        return $units;
    }

    /**
     * Calculate quantity_deducted based on unit sold and conversion rate
     */
    private function calculateQuantityDeducted(Product $product, ?string $unitSold, float $quantitySold, float $conversionRate): float
    {
        // If unit_sold matches purchase_unit, apply conversion rate
        if ($unitSold === $product->purchase_unit && $conversionRate > 1) {
            return $quantitySold * $conversionRate;
        }

        // Check if unit_sold has a conversion in unit_conversions table
        $unitConversion = $product->unitConversions()
            ->where('unit', $unitSold)
            ->first();

        if ($unitConversion) {
            return $quantitySold * $unitConversion->conversion_rate;
        }

        // Default: quantity sold equals quantity deducted (same unit)
        return $quantitySold;
    }

    /**
     * Prepare the data for validation.
     * Calculate total from items if not provided.
     */
    protected function prepareForValidation(): void
    {
        $items = $this->input('items', []);
        $total = 0;

        foreach ($items as &$item) {
            $qty = (float) ($item['quantity_sold'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $item['total_price'] = $qty * $price;
            $total += $item['total_price'];
        }

        $this->merge([
            'items' => $items,
            'total' => $total,
        ]);
    }
}
