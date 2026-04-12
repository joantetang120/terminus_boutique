<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('product.create');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'unit' => 'required|in:carton,boite,paquet,piece',
            'purchase_unit' => 'nullable|in:carton,boite,paquet,piece|different:unit',
            'purchase_conversion_rate' => 'nullable|integer|min:1|required_with:purchase_unit',
            'sale_unit' => 'nullable|in:carton,boite,paquet,piece|different:unit',
            'sale_conversion_rate' => 'nullable|integer|min:1|required_with:sale_unit',
            'current_stock' => 'required|integer|min:0',
            'initial_stock_unit' => 'nullable|in:carton,boite,paquet,piece',
            'initial_stock_conversion_rate' => 'nullable|integer|min:1|required_with:initial_stock_unit',
            'alert_threshold' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}
