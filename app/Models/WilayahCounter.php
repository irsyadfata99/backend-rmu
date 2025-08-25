<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WilayahCounter extends Model
{
    use HasFactory;

    protected $fillable = [
        'wilayah_code',
        'current_number'
    ];

    protected $casts = [
        'current_number' => 'integer',
    ];

    // Relationship to get all members for this wilayah (if needed)
    public function members()
    {
        return $this->hasMany(Member::class, 'wilayah', 'wilayah_code');
    }
}