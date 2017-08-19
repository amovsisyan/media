<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostParts extends Model
{
    protected $table = 'post_parts';
    public $timestamps = false;

    protected $fillable = [
        'head', 'body', 'foot'
    ];

    public function post(){
        return $this->belongsTo('App\Post');
    }
}
