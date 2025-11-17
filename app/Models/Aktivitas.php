<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aktivitas extends Model
{
    protected $table = 'aktivitas';
    protected $primaryKey = 'id_aktivitas';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id_aktivitas',
        'kode',
        'nrm',
        'status',
        'tanggal_peminjaman',
        'jatuh_tempo'
    ];

    protected $casts = [
        'tanggal_peminjaman' => 'date',
        'jatuh_tempo' => 'date'
    ];

    // Relationship dengan tabel mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nrm', 'nrm');
    }

    // Relationship dengan tabel koleksi
    public function koleksi()
    {
        return $this->belongsTo(Koleksi::class, 'kode', 'kode');
    }
}
