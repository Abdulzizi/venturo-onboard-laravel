<?php

namespace App\Http\Requests\Sale;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SaleRequest extends FormRequest
{
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
            'm_customer_id' => 'required|uuid|exists:m_customers,id',
            'date' => 'nullable|date',
            'product_detail' => 'required|array|min:1',
            'product_detail.*.m_product_id' => 'required|uuid|exists:m_products,id',
            'product_detail.*.m_product_detail_id' => 'required|uuid|exists:m_product_details,id',
            'product_detail.*.total_item' => 'required|integer|min:1',
            'product_detail.*.price' => 'required|numeric|min:0',
        ];
    }

    private function updateRules(): array
    {
        return [
            'id' => 'required|uuid|exists:t_sales,id', // sale ID should be UUID and exist
            'date' => 'nullable|date',
            'm_customer_id' => 'required|uuid|exists:m_customers,id',
            'product_detail' => 'required|array|min:1',
            'product_detail.*.m_product_id' => 'required|uuid|exists:m_products,id',
            'product_detail.*.m_product_detail_id' => 'required|uuid|exists:m_product_details,id',
            'product_detail.*.total_item' => 'required|integer|min:1',
            'product_detail.*.price' => 'required|numeric|min:0'
        ];
    }

    public function attributes()
    {
        return [
            'm_customer_id' => 'customer ID',
            'product_detail.*.m_product_id' => 'product ID',
            'product_detail.*.m_product_detail_id' => 'product detail ID',
            'product_detail.*.total_item' => 'total item',
            'product_detail.*.price' => 'price',
        ];
    }

    // Custom message's
    public function messages()
    {
        return [
            'm_customer_id.required' => 'The customer ID is required.',
            'm_customer_id.uuid' => 'The customer ID must be a valid UUID.',
            'm_customer_id.exists' => 'The selected customer ID does not exist.',
            'product_detail.required' => 'The product details are required.',
            'product_detail.array' => 'The product details should be an array.',
            'product_detail.*.m_product_id.required' => 'The product ID is required.',
            'product_detail.*.m_product_id.uuid' => 'The product ID must be a valid UUID.',
            'product_detail.*.m_product_id.exists' => 'The selected product ID does not exist.',
            'product_detail.*.m_product_detail_id.required' => 'The product detail ID is required.',
            'product_detail.*.m_product_detail_id.uuid' => 'The product detail ID must be a valid UUID.',
            'product_detail.*.m_product_detail_id.exists' => 'The selected product detail ID does not exist.',
            'product_detail.*.total_item.required' => 'The total item is required.',
            'product_detail.*.total_item.integer' => 'The total item must be an integer.',
            'product_detail.*.total_item.min' => 'The total item must be at least 1.',
            'product_detail.*.price.required' => 'The price is required.',
            'product_detail.*.price.numeric' => 'The price must be a valid number.',
            'product_detail.*.price.min' => 'The price must be at least 0.',
        ];
    }
}
