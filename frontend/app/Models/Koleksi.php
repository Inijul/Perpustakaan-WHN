<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Koleksi extends Model
{
    protected $table = 'koleksi';
    protected $primaryKey = 'kode';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode',
        'kategori',
        'topik',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'lokasi_rak',
        'deskripsi',
        'tautan',
        'sampul'
    ];

    // Relationship dengan tabel aktivitas
    public function aktivitas()
    {
        return $this->hasMany(Aktivitas::class, 'kode', 'kode');
    }
}
