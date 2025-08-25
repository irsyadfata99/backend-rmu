<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust based on your auth requirements
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nikKtp' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]+$/',
                Rule::unique('members', 'nik_ktp')
            ],
            'namaLengkap' => [
                'required',
                'string',
                'min:3',
                'max:255'
            ],
            'alamatLengkap' => [
                'required',
                'string',
                'min:10',
                'max:1000'
            ],
            'wilayah' => [
                'required',
                'string',
                'size:3',
                Rule::exists('wilayah_counters', 'wilayah_code')
            ],
            'nomorWhatsapp' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^[0-9]+$/'
            ]
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nikKtp.required' => 'NIK KTP wajib diisi',
            'nikKtp.size' => 'NIK KTP harus 16 digit',
            'nikKtp.regex' => 'NIK KTP hanya boleh berisi angka',
            'nikKtp.unique' => 'NIK KTP sudah terdaftar',
            
            'namaLengkap.required' => 'Nama lengkap wajib diisi',
            'namaLengkap.min' => 'Nama lengkap minimal 3 karakter',
            'namaLengkap.max' => 'Nama lengkap maksimal 255 karakter',
            
            'alamatLengkap.required' => 'Alamat lengkap wajib diisi',
            'alamatLengkap.min' => 'Alamat lengkap minimal 10 karakter',
            'alamatLengkap.max' => 'Alamat lengkap maksimal 1000 karakter',
            
            'wilayah.required' => 'Wilayah wajib dipilih',
            'wilayah.exists' => 'Wilayah tidak valid',
            
            'nomorWhatsapp.required' => 'Nomor WhatsApp wajib diisi',
            'nomorWhatsapp.min' => 'Nomor WhatsApp minimal 10 digit',
            'nomorWhatsapp.max' => 'Nomor WhatsApp maksimal 15 digit',
            'nomorWhatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nikKtp' => 'NIK KTP',
            'namaLengkap' => 'nama lengkap',
            'alamatLengkap' => 'alamat lengkap',
            'wilayah' => 'wilayah',
            'nomorWhatsapp' => 'nomor WhatsApp'
        ];
    }
}