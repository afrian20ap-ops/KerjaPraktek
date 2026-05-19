<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanLapangan extends Model
{
    protected $table = 'laporan_lapangan';
    protected $guarded = [];

    protected $casts = [
        'tanggal'         => 'date',
        'foto_paths'      => 'array',
        'foto_deskripsis' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
