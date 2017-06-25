<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table = 'subcategories';

    public function category(){
        return $this->belongsTo('App\Category', 'categ_id');
    }

    public function posts(){
        return $this->hasMany('App\Post', 'subcateg_id', 'id');
    }
}