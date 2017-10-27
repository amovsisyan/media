<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    protected $table = 'locale';
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];
}
