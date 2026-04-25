<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime:H:i',
        'jam_keluar' => 'datetime:H:i',
        'total_hari' => 'decimal:1',
        'dapat_uang_makan' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
