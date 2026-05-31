<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StockEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('stock.create');
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'input_unit' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (!$value) {
                        return;
                    }

                    $product = Product::with('purchaseConversions')->find($this->product_id);
                    if (!$product) {
                        return;
                    }

                    $allowedUnits = [$product->unit];

                    if ($product->purchase_unit) {
                        $allowedUnits[] = $product->purchase_unit;
                    }

                    foreach ($product->purchaseConversions as $conversion) {
                        $allowedUnits[] = $conversion->unit;
                    }

                    if (!in_array($value, array_unique($allowedUnits), true)) {
                        $fail("L'unité sélectionnée n'est pas valide pour ce produit.");
                    }
                },
            ],
            'note' => 'nullable|string|max:500',
            'unit_cost' => 'nullable|numeric|min:0',
            'total_cost' => 'nullable|numeric|min:0',
        ];
    }
}
