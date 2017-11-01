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

    public function postLocale(){
        return $this->hasMany('App\PostLocale', 'locale_id', 'id');
    }

    public function categoryLocale(){
        return $this->hasMany('App\CategoryLocale', 'locale_id', 'id');
    }

    public function subcategoryLocale(){
        return $this->hasMany('App\SubcategoryLocale', 'locale_id', 'id');
    }
}
