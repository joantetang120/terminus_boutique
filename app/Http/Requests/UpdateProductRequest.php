<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('product.edit');
    }

    public function rules(): array
    {
        $productId = $this->route('produit')?->id ?? $this->route('product')?->id;

        return [
            'name' => 'required|string|max:255|unique:products,name,' . $productId,
            'description' => 'nullable|string',
            'unit' => 'required|in:carton,boite,paquet,piece,sceau,sacs,palettes',
            'purchase_unit' => 'nullable|in:carton,boite,paquet,piece,sceau,sacs,palettes',
            'purchase_conversion_rate' => 'nullable|integer|min:1|required_with:purchase_unit',
            'sale_unit' => 'nullable|in:carton,boite,paquet,piece,sceau,sacs,palettes|different:unit',
            'sale_conversion_rate' => 'nullable|integer|min:1|required_with:sale_unit',
            
            // Base unit pricing
            'base_sale_price' => 'nullable|numeric|min:0',
            'base_sale_margin_percentage' => 'nullable|numeric|min:0|max:100',
            'purchase_price' => 'nullable|numeric|min:0',

            'sale_conversions' => 'nullable|array',
            'sale_conversions.*.unit' => 'required_with:sale_conversions|in:carton,boite,paquet,piece,sceau,sacs,palettes|different:unit',
            'sale_conversions.*.conversion_rate' => 'required_with:sale_conversions|integer|min:1',
            'sale_conversions.*.sale_price' => 'nullable|numeric|min:0',
            'sale_conversions.*.sale_margin_percentage' => 'nullable|numeric|min:0|max:100',
            'current_stock' => 'required|integer|min:0',
            'alert_threshold' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}
