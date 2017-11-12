<?php

namespace App;

use App\Http\Controllers\Helpers\Helpers;
use Illuminate\Database\Eloquent\Model;

class PostLocale extends Model
{
    protected $table = 'posts_locale';

    protected $fillable = [
        'header', 'text', 'image', 'post_id', 'locale_id'
    ];

    public function post(){
        return $this->belongsTo('App\Post', 'post_id');
    }

    public function locale(){
        return $this->belongsTo('App\Locale', 'locale_id');
    }

    public function postParts(){
        return $this->hasMany('App\PostParts', 'posts_locale_id');
    }

    public static function getLimitedLocalizedPosts($recentPostCount)
    {
        $localeId = Helpers::getLocaleIdFromSession();

        $result = self::orderBy('created_at', 'desc')
            ->where('locale_id', $localeId)
            ->take($recentPostCount)
            ->with(['post' => function ($query) {
                $query->with(['subcategory' => function ($query) {
                    $query->with('category');
                }]);
            }])
            ->get();

        return $result;
    }
}