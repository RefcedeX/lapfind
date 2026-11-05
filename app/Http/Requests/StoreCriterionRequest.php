<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCriterionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:10', 'unique:criteria,code'],
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'in:benefit,cost'],
            'unit' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get Indonesian error messages for defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Kode kriteria wajib diisi.',
            'code.max' => 'Kode kriteria maksimal :max karakter.',
            'code.unique' => 'Kode kriteria ini sudah digunakan. Gunakan kode lain (misal C7).',
            'name.required' => 'Nama kriteria wajib diisi.',
            'name.max' => 'Nama kriteria maksimal :max karakter.',
            'type.required' => 'Tipe kriteria wajib dipilih.',
            'type.in' => 'Tipe kriteria harus berupa benefit atau cost.',
            'unit.max' => 'Satuan kriteria maksimal :max karakter.',
            'description.max' => 'Deskripsi kriteria maksimal :max karakter.',
        ];
    }
}
