<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('article');

        return [
            'name'           => ['sometimes', 'string', 'max:255'],
            'reference'      => ['sometimes', 'nullable', 'string', Rule::unique('articles', 'reference')->ignore($id)],
            'barcode'        => ['sometimes', 'nullable', 'string', Rule::unique('articles', 'barcode')->ignore($id)],
            'quantity'       => ['sometimes', 'integer', 'min:0'],
            'min_quantity'   => ['sometimes', 'integer', 'min:0'],
            'article_status' => ['sometimes', Rule::in(['Nouveau', 'Ancien'])],
            'unit'           => ['sometimes', 'nullable', 'string', 'max:50'],
            'brand'          => ['sometimes', 'nullable', 'string', 'max:100'],
            'nature'         => ['sometimes', 'nullable', 'string', 'max:100'],
            'supplier'       => ['sometimes', 'nullable', 'string', 'max:255'],
            'ocp_code'       => ['sometimes', 'nullable', 'string', 'max:100'],
            'description'    => ['sometimes', 'nullable', 'string'],
            'category_id'    => ['sometimes', 'nullable', 'exists:categories,id'],
        ];
    }
}
