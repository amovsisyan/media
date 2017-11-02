<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    protected $table = 'hashtags';
    public $timestamps = false;

    protected $fillable = [
        'alias'
    ];

    public function posts(){
        return $this->belongsToMany('App\Post', 'post_hashtag', 'hashtags_id', 'post_id');
    }

    public function hashtagsLocale(){
        return $this->hasMany('App\HashtagLocale', 'hashtag_id', 'id');
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getHashtagBuilderByID($id)
    {
        return self::where('id', $id);
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function getHashtagsBuilderLikeHashtag($hashtag)
    {
        return self::where('hashtag', 'like', "%$hashtag%");
    }

    /**
     * @param $alias
     * @return mixed
     */
    public static function getHashtagsBuilderLikeAlias($alias)
    {
        return self::where('alias', 'like', "%$alias%");
    }

    /**
     * @param $id
     * @param $updateArr
     * @return mixed
     */
    public static function updHashtagByID($id, $updateArr)
    {
        return self::where('id', $id)->update($updateArr);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function delHashtagById($id)
    {
        return self::where('id', $id)->delete();
    }
}
