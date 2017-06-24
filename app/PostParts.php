<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostParts extends Model
{
    protected $table = 'post_parts';

    public function post(){
        return $this->belongsTo('App\Post');
    }
}
