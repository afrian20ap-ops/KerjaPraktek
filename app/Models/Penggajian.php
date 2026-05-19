<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penggajian extends Model
{
    protected $table = 'penggajian';
    protected $guarded = [];

    protected $casts = [
        'periode_mulai' => 'date',
        'periode_akhir' => 'date',
        'total_kehadiran_hari' => 'decimal:1',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
