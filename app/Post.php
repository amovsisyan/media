<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'alias', 'subcateg_id'
    ];

    public function postLocale(){
        return $this->hasMany('App\PostLocale', 'post_id', 'id');
    }

    public function subcategory(){
        return $this->belongsTo('App\Subcategory', 'subcateg_id');
    }

    public function postParts(){
        return $this->hasMany('App\PostParts', 'post_id', 'id');
    }

    public function hashtags(){
        return $this->belongsToMany('App\Hashtag', 'post_hashtag', 'post_id');
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getPostBuilderByID($id)
    {
        return self::where('id', $id);
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function getPostsBuilderLikeHeader($header)
    {
        return self::where('header', 'like', "%$header%");
    }

    /**
     * @param $alias
     * @return mixed
     */
    public static function getPostsBuilderLikeAlias($alias)
    {
        return self::where('alias', 'like', "%$alias%");
    }

    /**
     * @param $request
     * @param $post
     */
    public static function hashtagsEdited($hashtags, $post)
    {
        $post->hashtags()->detach();
        $post->hashtags()->attach($hashtags);
    }
}
