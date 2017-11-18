<?php

namespace App;

use App\Http\Controllers\Helpers\Helpers;
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

    public static function getPostsLocaledByHashtagAlias($alias)
    {
        $localeId = Helpers::getLocaleIdFromSession();

        $result = self::where('alias', $alias)
            ->with(['posts' => function ($query) use ($localeId) {
                $query->with(
                    [
                        'postLocale' => function ($query) use ($localeId) {
                            $query->where('locale_id', $localeId);
                        },
                        'subcategory' => function ($query) {
                            $query->with(['category' => function ($query) {
                            }]);
                        }
                    ]
                );
            },
                'hashtagsLocale' => function ($query) use ($localeId) {
                    $query->where('locale_id', $localeId);
                }
            ])
            ->get();

        return $result;
    }

    public static function getAllHashtags()
    {
        return self::select('id', 'alias')->orderBy('alias')->get();
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
