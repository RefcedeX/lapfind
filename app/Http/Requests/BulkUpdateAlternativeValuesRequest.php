<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateAlternativeValuesRequest extends FormRequest
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
            'values' => ['required', 'array'],
            'values.*' => ['required', 'array'],
            'values.*.*' => ['required', 'numeric', 'min:0'],
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
            'values.required' => 'Data matriks nilai alternatif wajib dikirimkan.',
            'values.*.*.required' => 'Setiap sel nilai pada matriks wajib diisi.',
            'values.*.*.numeric' => 'Nilai pada matriks harus berupa angka numerik.',
            'values.*.*.min' => 'Nilai pada matriks tidak boleh negatif.',
        ];
    }
}
