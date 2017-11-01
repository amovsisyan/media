<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostLocale extends Model
{
    protected $table = 'post_locale';

    protected $fillable = [
        'header', 'text', 'image', 'post_id', 'locale_id'
    ];

    public function post(){
        return $this->belongsTo('App\Post', 'post_id');
    }

    public function locale(){
        return $this->belongsTo('App\Locale', 'locale_id');
    }

    public static function getLimitedLocalizedPosts($localeId, $recentPostCount)
    {
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
