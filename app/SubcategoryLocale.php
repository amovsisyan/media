<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubcategoryLocale extends Model
{
    protected $table = 'subcategories_locale';

    protected $fillable = [
        'name'
    ];

    public function subcategory(){
        return $this->belongsTo('App\Subcategory', 'subcateg_id');
    }

    public function locale(){
        return $this->belongsTo('App\Locale', 'locale_id');
    }
}
