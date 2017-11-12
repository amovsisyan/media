<?php

namespace App;

use App\Http\Controllers\Helpers\Helpers;
use Illuminate\Database\Eloquent\Model;

class CategoryLocale extends Model
{
    protected $table = 'categories_locale';

    protected $fillable = [
        'name', 'locale_id'
    ];

    public function category(){
        return $this->belongsTo('App\Category', 'categ_id');
    }

    public function locale(){
        return $this->belongsTo('App\Locale', 'locale_id');
    }

    /**
     * User Side Navbar Localized
     * @param $localeId
     * @return mixed
     */
    public static function getCategorySubcategoryLocalized()
    {
        $localeId = Helpers::getLocaleIdFromSession();

        $result = self::select('id', 'name', 'categ_id')
            ->where('locale_id', $localeId)
            ->with(['category' => function ($query) use ($localeId) {
                $query->with(['subcategories' => function ($query) use ($localeId) {
                    $query->with(['subcategoriesLocale' => function ($query) use ($localeId) {
                        $query->where('locale_id', $localeId);
                    }]);
                }]);
            }])
            ->get();

        return $result;
    }

    /**
     * Update CategoryLocale selecting by ID and updating by updateArr
     * @param $id
     * @param $updateArr
     * @return mixed
     */
    public static function updLocaleCategoryByID($id, $updateArr)
    {
        return self::where('id', $id)->update($updateArr);
    }
}
