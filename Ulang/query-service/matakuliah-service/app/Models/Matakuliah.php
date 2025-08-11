<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Matakuliah extends Eloquent
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'matakuliah';
    protected $fillable = [
        'kodematkul', 'namamatkul', 'semester',
        'sks', 'jam', 'kodeprodi','namaprodi',
    ];
}
