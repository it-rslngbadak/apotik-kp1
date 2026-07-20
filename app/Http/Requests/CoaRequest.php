<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoaRequest extends FormRequest
{
    /**
     * Sesuaikan kalau memang ada pengecekan izin lain (misal role),
     * middleware 'auth' di route sudah menjamin user login.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_coa'         => 'required|in:Pendapatan,Biaya',
            'kategori'         => 'required|in:Tindakan,Farmalkes,Umum',
            'eselon'            => 'nullable|string|max:100',
            'jenis_tarif'            => 'nullable|string|max:100',
            'desc_transaksi'    => 'required|string|max:255',
            'jumlah'            => 'required|integer|min:1',
            'satuan'            => 'required|string|max:50',
            'harga_satuan'      => 'required|numeric|min:0',
            'kode_transaksi_id' => 'required',
        ];
    }

    /**
     * Custom pesan error (opsional, sesuaikan/hapus kalau mau pakai default Laravel)
     */
    public function messages(): array
    {
        return [
            'jenis_coa.required'         => 'Jenis COA wajib dipilih',
            'jenis_coa.in'               => 'Jenis COA tidak valid',
            'desc_transaksi.required'    => 'Uraian pekerjaan wajib diisi',
            'jumlah.required'            => 'Jumlah wajib diisi',
            'jumlah.min'                 => 'Jumlah minimal 1',
            'satuan.required'            => 'Satuan wajib diisi',
            'harga_satuan.required'      => 'Harga satuan wajib diisi',
            'harga_satuan.min'           => 'Harga satuan tidak boleh minus',
            'kode_transaksi_id.required' => 'COA wajib dipilih',
            'kode_transaksi_id.exists'   => 'COA yang dipilih tidak valid',
        ];
    }

    /**
     * Karena request ini selalu dipanggil lewat AJAX (submitCoa di JS),
     * pastikan response failed validation tetap JSON (default FormRequest
     * sudah otomatis JSON untuk request yang expectsJson(), jadi ini
     * cuma jaga-jaga / eksplisit).
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException(
            $validator,
            response()->json([
                'message' => 'Data yang dikirim tidak valid',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
