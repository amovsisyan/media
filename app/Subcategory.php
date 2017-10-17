<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table = 'subcategories';

    protected $fillable = [
        'name', 'alias', 'categ_id'
    ];

    public function category(){
        return $this->belongsTo('App\Category', 'categ_id', 'id');
    }

    public function posts(){
        return $this->hasMany('App\Post', 'subcateg_id', 'id');
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getSubCategoryBuilderByID($id)
    {
        return self::where('id', $id);
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function getSubCategoriesBuilderLikeName($name)
    {
        return self::where('name', 'like', "%$name%");
    }

    /**
     * @param $alias
     * @return mixed
     */
    public static function getSubCategoriesBuilderLikeAlias($alias)
    {
        return self::where('alias', 'like', "%$alias%");
    }

    /**
     * Delete Category by ID's array
     * @param array $ids
     * @return mixed
     */
    public static function delSubCategoriesByIDs($ids = [])
    {
        return self::whereIn('id', $ids)->delete();
    }
}
