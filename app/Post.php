<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    public function subcategory(){
        return $this->belongsTo('App\Subcategory');
    }

    public function postParts(){
        return $this->hasMany('App\PostParts', 'post_id', 'id');
    }
}
