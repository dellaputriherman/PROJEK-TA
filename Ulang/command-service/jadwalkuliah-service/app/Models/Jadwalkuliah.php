<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKuliah extends Model
{
    use HasFactory;

    protected $table = 'jadwalkuliah';

    protected $fillable = [
        'kodekelas',
        'kodematkul',
        'hari',
        'jammulai',
        'jamselesai',
        'ruangan',
        'nip',
    ];

    public function kelasmaster()
    {
        return $this->belongsTo(Kelasmaster::class, 'kodekelas', 'kodekelas');
    }

    public function matakuliah()
    {
        return $this->belongsTo(Matakuliah::class, 'kodematkul', 'kodematkul');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'nip', 'nip');
    }
}
