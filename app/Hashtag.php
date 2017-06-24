<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    public function posts(){
        return $this->belongsToMany('App\Post', 'post_hashtag', 'hashtag_id');
    }
}
