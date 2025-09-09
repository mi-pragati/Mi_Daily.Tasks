<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('update', $this->route('product')) ?? false; }
    public function rules(): array {
        return [
            'product_category_id' => ['required','exists:product_categories,id'],
            'title'       => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price'       => ['required','numeric','min:0'],
            'stock'       => ['required','integer','min:0'],
            'status'      => ['required','in:draft,published'],
        ];
    }
}
