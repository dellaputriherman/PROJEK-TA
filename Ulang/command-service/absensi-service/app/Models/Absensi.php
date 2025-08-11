<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'nim',
        'kodekelas',
        'kodematkul',
        'tanggal',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }
      public function kelasmaster()
    {
        return $this->belongsTo(Kelasmaster::class, 'kodekelas', 'kodekelas');
    }
    public function matakuliah()
    {
        return $this->belongsTo(Matakuliah::class, 'kodematkul', 'kodematkul');
    }
}
