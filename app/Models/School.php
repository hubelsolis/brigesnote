<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{

    protected $table = 'schools';
    protected $primaryKey = 'codMod';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codMod',
        'nombreCenEdu',
        'cenPob',
        'dirCen',
        'director',
        'dis_crecer',
        'dis_juntos',
    ];
}
