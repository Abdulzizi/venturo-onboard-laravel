<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class UserRequest extends FormRequest
{
    use ConvertsBase64ToFiles; // Library untuk convert base64 menjadi File

    public $validator;

    /**
     * Setting custom attribute pesan error yang ditampilkan
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'password' => 'Kolom Password',
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

    public function messages(): array
    {
        return [
            // Common fields
            'name.required' => 'Nama pengguna wajib diisi.',
            'name.max' => 'Nama pengguna tidak boleh lebih dari 100 karakter.',
            'photo.file' => 'Foto pengguna harus berupa file yang valid.',
            'photo.image' => 'Foto pengguna harus berupa gambar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email harus berupa alamat email yang valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi harus memiliki minimal 6 karakter.',
            'phone_number.numeric' => 'Nomor telepon harus berupa angka.',
            'm_user_roles_id.required' => 'Peran pengguna wajib diisi.',

            // Update specific fields
            'password.nullable' => 'Kata sandi hanya boleh diisi jika ingin mengubahnya.',
            'phone_number.nullable' => 'Nomor telepon bersifat opsional.',
            'm_user_roles_id.nullable' => 'Peran pengguna bersifat opsional.',
        ];
    }


    /**
     * Create rules.
     *
     * @return array
     */
    private function createRules(): array
    {
        return [
            'name' => 'required|max:100',
            'photo' => 'nullable|file|image',
            'email' => 'required|email|unique:m_users',
            'password' => 'required|min:6',
            'phone_number' => 'numeric',
            'm_user_roles_id' => 'required',
        ];
    }

    /**
     * Update rules.
     *
     * @return array
     */
    private function updateRules(): array
    {
        return [
            'name' => 'max:100',
            'photo' => 'nullable|file|image',
            'email' => 'email|unique:m_users,email,' . $this->input('id'),
            'password' => 'nullable|min:6',
            'phone_number' => 'nullable|numeric',
            'm_user_roles_id' => 'nullable',
        ];
    }

    /**
     * inisialisasi key "photo" dengan value base64 sebagai "FILE"
     */
    protected function base64FileKeys(): array
    {
        return [
            'photo' => 'foto-user.jpg',
        ];
    }
}
