<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StockExitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('stock.create');
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    $product = Product::find($this->product_id);
                    if (!$product) {
                        return;
                    }
                    
                    // Convert quantity to base unit if input_unit is provided
                    $baseQuantity = $value;
                    $inputUnit = $this->input('input_unit');
                    if ($inputUnit && $inputUnit !== $product->unit) {
                        try {
                            $baseQuantity = $product->convertToBaseUnit($value, $inputUnit);
                        } catch (\Exception $e) {
                            $fail('Unité invalide pour ce produit');
                            return;
                        }
                    }
                    
                    if ($product->current_stock < $baseQuantity) {
                        $availableInInputUnit = $inputUnit ? $product->convertFromBaseUnit($product->current_stock, $inputUnit) : $product->current_stock;
                        $unitLabel = $inputUnit ?? $product->unit;
                        $fail('Stock insuffisant : ' . $availableInInputUnit . ' ' . $unitLabel . ' disponibles');
                    }
                },
            ],
            'input_unit' => 'nullable|string|in:carton,boite,paquet,piece,sceau,sacs,palettes',
            'note' => 'required|string|min:5|max:500',
        ];
    }
}
