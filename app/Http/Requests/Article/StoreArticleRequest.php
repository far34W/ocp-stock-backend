<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'reference'      => ['nullable', 'string', 'unique:articles,reference'],
            'barcode'        => ['nullable', 'string', 'unique:articles,barcode'],
            'quantity'       => ['required', 'integer', 'min:0'],
            'min_quantity'   => ['required', 'integer', 'min:0'],
            'article_status' => ['nullable', Rule::in(['Nouveau', 'Ancien'])],
            'unit'           => ['nullable', 'string', 'max:50'],
            'brand'          => ['nullable', 'string', 'max:100'],
            'nature'         => ['nullable', 'string', 'max:100'],
            'supplier'       => ['nullable', 'string', 'max:255'],
            'ocp_code'       => ['nullable', 'string', 'max:100'],
            'description'    => ['nullable', 'string'],
            'category_id'    => ['nullable', 'exists:categories,id'],
        ];
    }
}
