<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCriterionRequest extends FormRequest
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
        $criterionId = $this->route('criterion')?->id ?? $this->route('criterion');

        return [
            'code' => ['required', 'string', 'max:10', Rule::unique('criteria', 'code')->ignore($criterionId)],
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
            'code.unique' => 'Kode kriteria ini sudah digunakan oleh kriteria lain.',
            'name.required' => 'Nama kriteria wajib diisi.',
            'name.max' => 'Nama kriteria maksimal :max karakter.',
            'type.required' => 'Tipe kriteria wajib dipilih.',
            'type.in' => 'Tipe kriteria harus berupa benefit atau cost.',
            'unit.max' => 'Satuan kriteria maksimal :max karakter.',
            'description.max' => 'Deskripsi kriteria maksimal :max karakter.',
        ];
    }
}
