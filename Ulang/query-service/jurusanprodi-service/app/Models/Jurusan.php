<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Jurusan extends Model
{
    protected $connection = 'mongodb'; // pastikan .env & config/database.php disesuaikan
    protected $collection = 'jurusan';

    protected $fillable = [
        'kodejurusan',
        'namajurusan',
    ];
}
