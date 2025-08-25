<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'nik_ktp',
        'nama_lengkap',
        'alamat_lengkap',
        'wilayah',
        'nomor_whatsapp',
        'status',
        'registration_date'
    ];

    protected $casts = [
        'registration_date' => 'datetime',
    ];

    // Add accessors to match frontend expectations
    protected $appends = ['wilayah_name'];

    public function getWilayahNameAttribute()
    {
        $wilayahOptions = [
            'BDG' => 'Bandung',
            'KBG' => 'Kabupaten Bandung',
            'KBB' => 'Kabupaten Bandung Barat',
            'KBT' => 'Kabupaten Bandung Timur',
            'CMH' => 'Cimahi',
            'GRT' => 'Garut',
            'KGU' => 'Kabupaten Garut Utara',
            'KGS' => 'Kabupaten Garut Selatan',
            'SMD' => 'Sumedang',
            'TSM' => 'Tasikmalaya',
            'SMI' => 'Kota Sukabumi',
            'KSI' => 'Kabupaten Sukabumi',
            'KSU' => 'Kabupaten Sukabumi Utara',
            'CJR' => 'Cianjur',
            'BGR' => 'Bogor',
            'KBR' => 'Kabupaten Bogor',
            'YMG' => 'Yamughni',
            'PMB' => 'Pembina'
        ];
        
        return $wilayahOptions[$this->wilayah] ?? $this->wilayah;
    }

    // Transform data for API responses to match frontend expectations
    public function toArray()
    {
        $array = parent::toArray();
        
        return [
            'id' => $this->member_id,
            'memberId' => $this->member_id,
            'nikKtp' => $this->nik_ktp,
            'namaLengkap' => $this->nama_lengkap,
            'alamatLengkap' => $this->alamat_lengkap,
            'wilayah' => $this->wilayah,
            'wilayahName' => $this->wilayah_name,
            'nomorWhatsapp' => $this->nomor_whatsapp,
            'status' => $this->status,
            'registrationDate' => $this->registration_date->toISOString(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    protected $hidden = [
        // Hide sensitive data if needed
    ];

    // Static method to get wilayah options (useful for validation and other purposes)
    public static function getWilayahOptions()
    {
        return [
            'BDG' => 'Bandung',
            'KBG' => 'Kabupaten Bandung',
            'KBB' => 'Kabupaten Bandung Barat',
            'KBT' => 'Kabupaten Bandung Timur',
            'CMH' => 'Cimahi',
            'GRT' => 'Garut',
            'KGU' => 'Kabupaten Garut Utara',
            'KGS' => 'Kabupaten Garut Selatan',
            'SMD' => 'Sumedang',
            'TSM' => 'Tasikmalaya',
            'SMI' => 'Kota Sukabumi',
            'KSI' => 'Kabupaten Sukabumi',
            'KSU' => 'Kabupaten Sukabumi Utara',
            'CJR' => 'Cianjur',
            'BGR' => 'Bogor',
            'KBR' => 'Kabupaten Bogor',
            'YMG' => 'Yamughni',
            'PMB' => 'Pembina'
        ];
    }

    // Static method to get valid wilayah codes
    public static function getValidWilayahCodes()
    {
        return array_keys(self::getWilayahOptions());
    }

    // Relationships can be added here if needed
    // For example, if you have a Wilayah model:
    // public function wilayahInfo()
    // {
    //     return $this->belongsTo(Wilayah::class, 'wilayah', 'code');
    // }
}