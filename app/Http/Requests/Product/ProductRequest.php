<?php

namespace App\Http\Requests\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class ProductRequest extends FormRequest
{
    use ConvertsBase64ToFiles;
    public $validator;

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        }

        return $this->updateRules();
    }

    private function createRules(): array
    {
        return [
            'name' => 'required|max:150',
            'price' => 'required|numeric',
            'photo' => 'nullable|file|image',
            'is_available' => 'required|numeric|max:1',
            'product_category_id' => 'required',
            'details.*.type' => 'required',
            'details.*.description' => 'required',
            'details.*.price' => 'numeric',
        ];
    }

    private function updateRules(): array
    {
        return [
            'id' => 'required',
            'name' => 'required|max:150',
            'price' => 'required|numeric',
            'photo' => 'nullable|file|image',
            'is_available' => 'required|numeric|max:1',
            'product_category_id' => 'required'
        ];
    }

    protected function base64FileKeys(): array
    {
        return [
            'photo' => 'foto-product.jpg',
        ];
    }

    public function attributes()
    {
        return [
            'is_available' => 'Status',
            'product_category_id' => 'Category'
        ];
    }
}
