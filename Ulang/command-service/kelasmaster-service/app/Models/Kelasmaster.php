<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelasmaster extends Model
{
    use HasFactory;

    protected $table = 'kelasmaster';

    protected $fillable = [
        'kodekelas',
        'namakelas',
        'kodejurusan',
        'kodeprodi',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'kodeprodi', 'kodeprodi');
    }
      public function jurusan()
    {
        return $this->belongsTo(Prodi::class, 'kodejurusan', 'kodejurusan');
    }
}
