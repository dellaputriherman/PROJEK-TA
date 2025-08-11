<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $fillable = [
        'nim',
        'kodekelas',
        'semester',
        'tahunajaran',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    public function kelasmaster()
    {
        return $this->belongsTo(Kelasmaster::class, 'kodekelas', 'kodekelas');
    }
}
