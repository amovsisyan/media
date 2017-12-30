<?php
namespace App\Http\Controllers\Services\Pagination;

use Illuminate\Support\Facades\Input;

class PaginationService
{
    const PER_PAGE_WELCOME = 9;
    const PER_PAGE_SUBCATEGORY = 9;

    public static function getWelcomePerPage()
    {
        return self::PER_PAGE_WELCOME;
    }

    public static function getSubcategoryPerPage()
    {
        return self::PER_PAGE_SUBCATEGORY;
    }

    public static function makeWelcomePagination($postsLocale)
    {
        $pagination = [];

        if (($total = $postsLocale->total()) > self::PER_PAGE_WELCOME) {
            $pagination = [
                'total' => $total,
                'lastPage' => $postsLocale->lastPage(),
                'perPage' => $postsLocale->perPage(),
                'currentPage' => $postsLocale->currentPage()
            ];
        }

        return $pagination;
    }

    public static function makeSubcategoryPagination($subcategory) // for now have no other solution
    {
        $pagination = [];

        if (($total = count($subcategory['posts'])) > self::PER_PAGE_SUBCATEGORY) {
            $pagination = [
                'total' => $total,
                'lastPage' => ceil($total / self::PER_PAGE_SUBCATEGORY),
                'perPage' => self::PER_PAGE_SUBCATEGORY,
                'currentPage' => (int)Input::get('page', 1)
            ];
        }

        return $pagination;
    }
}