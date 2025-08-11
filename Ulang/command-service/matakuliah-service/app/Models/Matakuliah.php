<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matakuliah extends Model
{
    use HasFactory;

    protected $table = 'matakuliah';

    protected $fillable = [
        'kodematkul',
        'namamatkul',
        'semester',
        'sks',
        'jam',
        'kodeprodi',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'kodeprodi', 'kodeprodi');
    }
}
