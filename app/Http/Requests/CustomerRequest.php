<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class CustomerRequest extends FormRequest
{
    use ConvertsBase64ToFiles;

    public $validator;

    public function attributes()
    {
        return [
            'name' => 'Kolom Nama Pelanggan',
            'address' => 'Kolom Alamat',
            'photo' => 'Kolom Foto',
            'phone' => 'Kolom Nomor Telepon',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Define validation rules.
     *
     * @return array
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        }

        return $this->updateRules();
    }

    /**
     * Rules for creating a customer.
     *
     * @return array
     */
    private function createRules(): array
    {
        return [
            'name' => 'required|max:100',
            'address' => 'nullable|string|max:255',
            'photo' => 'nullable|file|image',
            'phone' => 'nullable|numeric',
        ];
    }

    /**
     * Rules for updating a customer.
     *
     * @return array
     */
    private function updateRules(): array
    {
        return [
            'm_user_id' => 'required|exists:m_users,id',
            'id' => 'required|exists:m_customers,id',
            'email' => 'required|email',
            'name' => 'sometimes|max:100',
            'address' => 'nullable|string|max:255',
            'photo' => 'nullable|file|image',
            'phone' => 'nullable|numeric',
            'password' => 'nullable|min:8'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama pelanggan harus diisi.',
            'name.max' => 'Nama pelanggan tidak boleh lebih dari 100 karakter.',
            'address.max' => 'Alamat tidak boleh lebih dari 255 karakter.',
            'photo.image' => 'Foto harus berupa gambar yang valid.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
        ];
    }

    /**
     * Initialize key "photo" with a default filename for base64 conversions.
     *
     * @return array
     */
    protected function base64FileKeys(): array
    {
        return [
            'photo' => 'customer-photo.jpg',
        ];
    }
}
