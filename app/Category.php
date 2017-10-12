<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name', 'alias'
    ];

    public function subcategories(){
        return $this->hasMany('App\Subcategory', 'categ_id', 'id');
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getCategoryBuilderByID($id)
    {
        return self::where('id', $id);
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function getCategoriesBuilderLikeName($name)
    {
        return self::where('name', 'like', "%$name%");
    }

    /**
     * @param $alias
     * @return mixed
     */
    public static function getCategoriesBuilderLikeAlias($alias)
    {
        return self::where('alias', 'like', "%$alias%");
    }

    /**
     * Delete Category by ID's array
     * @param array $ids
     * @return mixed
     */
    public static function delCategoriesByIDs($ids = [])
    {
        return self::whereIn('id', $ids)->delete();
    }

    /**
     * Update Category selecting by ID and updating by updateArr
     * @param $id
     * @param $updateArr
     * @return mixed
     */
    public static function updCategoryByID($id, $updateArr)
    {
        return self::where('id', $id)->update($updateArr);
    }

    /**
     * @param $id
     * @param $createArr
     * @return mixed
     */
    public static function createSubCategoryByID($id, $createArr)
    {
        return self::getCategoryBuilderByID($id)->first()->subcategories()->create($createArr);
    }
}
