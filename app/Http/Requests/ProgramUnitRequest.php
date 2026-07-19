<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProgramUnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_program' => 'required|string|max:255',
            'ket_program'  => 'nullable|string',
            'kategori' => 'required|in:Regular,Work Program',
            'bulan' => 'required_if:kategori,Work Program|nullable|integer|min:1|max:12',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_program.required' => 'Nama program wajib diisi',
            'nama_program.max'      => 'Nama program maksimal 255 karakter',
        ];
    }
}
