<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;
    protected $connection = 'jurusanprodi';
    protected $table = 'prodi';
    protected $fillable = [
        'kodeprodi',
        'namaprodi',
        'kodejurusan',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'kodejurusan', 'kodejurusan');
    }

    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'kodeprodi', 'kodeprodi');
    }

    public function matakuliah()
    {
        return $this->hasMany(Matakuliah::class, 'kodeprodi', 'kodeprodi');
    }
}
