<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{

    protected $table = 'districts';
    protected $primaryKey = 'idDistrito';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'idDistrito',
        'nombreDistrito',
    ];
}
