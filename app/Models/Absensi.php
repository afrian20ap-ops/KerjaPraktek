<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tanggal'          => 'date',
        'total_hari'       => 'float',
        'jam_lembur'       => 'integer',
        'dapat_uang_makan' => 'boolean',
        'nominal_basic'    => 'float',
        'nominal_lembur'   => 'float',
        'nominal_makan'    => 'float',
        'nominal_kasbon'   => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
