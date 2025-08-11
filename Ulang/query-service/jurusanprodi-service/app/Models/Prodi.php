<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Prodi extends Eloquent
{
    // use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'prodi';

    protected $fillable = ['kodeprodi', 'namaprodi', 'kodejurusan'];
}
