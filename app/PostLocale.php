<?php

namespace App;

use App\Http\Controllers\Helpers\Helpers;
use App\Http\Controllers\Services\Pagination\PaginationService;
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

    public static function getLimitedLocalizedPosts()
    {
        $localeId = Helpers::getLocaleIdFromSession();

        $result = self::orderBy('created_at', 'desc')
            ->where('locale_id', $localeId)
            ->with(['post' => function ($query) {
                $query->with(['subcategory' => function ($query) {
                    $query->with('category');
                }]);
            }])
            ->paginate(PaginationService::getWelcomePerPage());

        return $result;
    }
}
