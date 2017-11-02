<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HashtagLocale extends Model
{
    protected $table = 'hashtags_locale';
    public $timestamps = false;

    protected $fillable = [
        'hashtag'
    ];

    public function hashtag(){
        return $this->belongsTo('App\Hashtag', 'hashtag_id', 'id');
    }
}
