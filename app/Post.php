<?php

namespace App;

use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Services\Locale\LocaleSettings;
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
        return $this->belongsToMany('App\Hashtag', 'post_hashtag', 'post_id', 'hashtags_id');
    }

    public static function postPartsWithHashtagsLocaleByAlias($postAlias)
    {
        $localeId = Helpers::getLocaleIdFromSession(); // todo IMPORTANT is it correct ?

        $result = self::where('alias', $postAlias)
            ->with(
                [
                    'postLocale' => function ($query) use ($localeId) {
                        $query->where('locale_id', $localeId)
                            ->with(['postParts']);
                    },
                    'hashtags' => function ($query) use ($localeId) {
                        $query->with(['hashtagsLocale' => function ($query) use ($localeId) {
                            $query->where('locale_id', $localeId);
                        }]);
                    }
                ]
            )->first();

        return $result;
    }

    public static function postWithPostLocaleHashtagsById($postId)
    {
        $result = self::where('id', $postId)
            ->with(
                [
                    'postLocale',
                    'hashtags'
                ]
            )->first();

        return $result;
    }

    public static function postWithPostLocalePostPartsById($postId)
    {
        $result = self::where('id', $postId)
            ->with(
                [
                    'postLocale' => function ($query) {
                        $query->with(['postParts']);
                    }
                ]
            )->first();

        return $result;
    }

    public static function postWithSubcategoryPostLocaleById($postId, $locale)
    {
        $localeId = LocaleSettings::getLocaleIdByName($locale);

        $result = self::where('id', $postId)
            ->with(
                [
                    'subcategory',
                    'postLocale' => function ($query) use ($localeId) {
                        $query->where('locale_id', $localeId);
                    },
                ]
            )->first();

        return $result;
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
