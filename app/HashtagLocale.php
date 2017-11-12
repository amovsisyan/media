<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HashtagLocale extends Model
{
    protected $table = 'hashtags_locale';
    public $timestamps = false;

    protected $fillable = [
        'hashtag', 'locale_id'
    ];

    public function hashtag(){
        return $this->belongsTo('App\Hashtag', 'hashtag_id', 'id');
    }

    /**
     * Update Hashtag selecting by ID and updating by updateArr
     * @param $id
     * @param $updateArr
     * @return mixed
     */
    public static function updLocaleHashtagByID($id, $updateArr)
    {
        return self::where('id', $id)->update($updateArr);
    }
}
