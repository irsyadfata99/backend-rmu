<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WilayahCounter extends Model
{
    use HasFactory;

    protected $table = 'wilayah_counters';

    protected $fillable = [
        'wilayah_code',
        'current_number'
    ];

    protected $casts = [
        'current_number' => 'integer'
    ];

    // Ensure wilayah_code is always uppercase
    public function setWilayahCodeAttribute($value)
    {
        $this->attributes['wilayah_code'] = strtoupper($value);
    }

    // Get next available number for a wilayah
    public static function getNextNumber($wilayahCode)
    {
        $counter = self::firstOrCreate(
            ['wilayah_code' => strtoupper($wilayahCode)],
            ['current_number' => 0]
        );
        
        return $counter->current_number + 1;
    }

    // Increment and get the next number
    public static function incrementAndGet($wilayahCode)
    {
        $counter = self::firstOrCreate(
            ['wilayah_code' => strtoupper($wilayahCode)],
            ['current_number' => 0]
        );
        
        $counter->increment('current_number');
        return $counter->current_number;
    }

    // Reset counter for a wilayah (useful for testing or admin purposes)
    public static function resetCounter($wilayahCode, $newValue = 0)
    {
        return self::updateOrCreate(
            ['wilayah_code' => strtoupper($wilayahCode)],
            ['current_number' => $newValue]
        );
    }

    // Get all counters with wilayah names
    public static function getAllWithNames()
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

        return self::all()->map(function ($counter) use ($wilayahOptions) {
            return [
                'wilayah_code' => $counter->wilayah_code,
                'wilayah_name' => $wilayahOptions[$counter->wilayah_code] ?? $counter->wilayah_code,
                'current_number' => $counter->current_number,
                'next_id' => $counter->wilayah_code . str_pad($counter->current_number + 1, 4, '0', STR_PAD_LEFT)
            ];
        });
    }
}