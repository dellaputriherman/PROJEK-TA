<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Jadwalkuliah extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'jadwalkuliah';

    protected $fillable = [
        'kodekelas',
        'namakelas',
        'kodematkul',
        'namamatkul',
        'hari',
        'jammulai',
        'jamselesai',
        'ruangan',
        'nip',
        'namadosen'
    ];
}
