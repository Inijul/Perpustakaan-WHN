<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    protected $primaryKey = 'nrm';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nrm',
        'nim',
        'namam'
    ];

    // Relationship dengan tabel aktivitas
    public function aktivitas()
    {
        return $this->hasMany(Aktivitas::class, 'nrm', 'nrm');
    }
}
