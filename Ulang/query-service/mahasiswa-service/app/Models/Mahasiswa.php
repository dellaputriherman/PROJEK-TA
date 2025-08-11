<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'mahasiswa';

    protected $fillable = [
        'nim',
        'nama',
        'tempatlahir',
        'tanggallahir',
        'jeniskelamin',
        'kodejurusan',
        'namajurusan',
        'kodeprodi',
        'namaprodi'
    ];
}
