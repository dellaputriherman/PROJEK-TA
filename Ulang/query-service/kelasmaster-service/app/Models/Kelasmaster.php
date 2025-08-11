<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Kelasmaster extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'kelasmaster';

    protected $fillable = [
        'kodekelas',
        'namakelas',
        'kodejurusan',
        'namajurusan',
        'kodeprodi',
        'namaprodi',
    ];
}
