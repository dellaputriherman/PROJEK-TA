<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;
    protected $connection = 'jurusanprodi';
    protected $table = 'jurusan';

    protected $fillable = [
        'kodejurusan',
        'namajurusan',
    ];

    public function prodi()
    {
        return $this->hasMany(Prodi::class, 'kodejurusan', 'kodejurusan');
    }

    public function dosen()
    {
        return $this->hasMany(Dosen::class, 'kodejurusan', 'kodejurusan');
    }

    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'kodejurusan', 'kodejurusan');
    }
}
