<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlternativeRequest extends FormRequest
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
        $alternativeId = $this->route('alternative')?->id ?? $this->route('alternative');

        return [
            'name' => ['required', 'string', 'max:150', Rule::unique('alternatives', 'name')->ignore($alternativeId)],
            'brand' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'values' => ['nullable', 'array'],
            'values.*' => ['nullable', 'numeric', 'min:0'],
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
            'name.required' => 'Nama laptop alternatif wajib diisi.',
            'name.max' => 'Nama laptop maksimal :max karakter.',
            'name.unique' => 'Nama laptop ini sudah ada dalam database.',
            'brand.required' => 'Merek laptop wajib diisi.',
            'brand.max' => 'Merek laptop maksimal :max karakter.',
            'price.required' => 'Harga laptop wajib diisi.',
            'price.numeric' => 'Harga laptop harus berupa angka valid.',
            'price.min' => 'Harga laptop minimal 0.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, webp.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'description.max' => 'Deskripsi maksimal :max karakter.',
            'values.*.numeric' => 'Nilai kriteria harus berupa angka numerik valid.',
            'values.*.min' => 'Nilai kriteria tidak boleh bernilai negatif.',
        ];
    }
}
