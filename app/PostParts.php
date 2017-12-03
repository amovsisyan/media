<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostParts extends Model
{
    protected $table = 'post_parts';
    public $timestamps = false;

    protected $fillable = [
        'head', 'body', 'foot', 'post_id'
    ];

    public function postLocale(){
        return $this->belongsTo('App\PostLocale', 'posts_locale_id');
    }

    public static function postPartsWithPostLocalePostSubcategoryById($id)
    {
        return self::where('id', $id)
            ->with(
                [
                    'postLocale' => function ($query) {
                        $query->with([
                            'post' => function ($query) {
                                $query->with(['subcategory']);
                            }
                        ]);
                    }
                ]
            )->first();
    }
}
