<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public $validator;

    /**
     * Setting custom attribute pesan error yang ditampilkan
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'Kolom Nama Role',
            'access' => 'Kolom Hak Akses',
        ];
    }

    /**
     * Tampilkan pesan error ketika validasi gagal
     *
     * @return void
     */
    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

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
            'name' => 'required|max:100|unique:m_user_roles,name',
            'access' => 'required|array',
        ];
    }

    private function updateRules(): array
    {
        return [
            'name' => 'max:100|unique:m_user_roles,name,' . $this->id,
            'access' => 'array',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama Role harus diisi.',
            'name.unique' => 'Nama Role sudah digunakan.',
            'access.required' => 'Kolom Hak Akses harus diisi.',
            'access.array' => 'Kolom Hak Akses harus berupa format array yang valid.',
        ];
    }
}
