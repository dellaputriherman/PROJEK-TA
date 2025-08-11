<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Absensi extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'absensi';

    protected $fillable = [
        'nim',
        'kodekelas',
        'kodematkul',
        'namamatkul',
        'tanggal',
        'status',
    ];
}
