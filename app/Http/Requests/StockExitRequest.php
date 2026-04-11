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
                    if ($product && $product->current_stock < $value) {
                        $fail('Stock insuffisant : ' . $product->current_stock . ' disponibles');
                    }
                },
            ],
            'note' => 'required|string|min:5|max:500',
        ];
    }
}
