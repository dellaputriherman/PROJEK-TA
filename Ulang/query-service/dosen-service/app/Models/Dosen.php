<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Dosen extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'dosen';

    protected $fillable = [
        'nip',
        'nama',
        'kodejurusan',
        'namajurusan',
    ];
}
