<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    protected $table = 'hashtags';
    public $timestamps = false;

    protected $fillable = [
        'hashtag', 'alias'
    ];

    public function posts(){
        return $this->belongsToMany('App\Post', 'post_hashtag', 'hashtag_id');
    }
}
