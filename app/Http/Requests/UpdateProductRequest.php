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
            'unit' => 'required|in:carton,boite,paquet,piece',
            'current_stock' => 'required|integer|min:0',
            'alert_threshold' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}
