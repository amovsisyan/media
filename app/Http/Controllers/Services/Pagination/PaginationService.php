<?php
namespace App\Http\Controllers\Services\Pagination;

class PaginationService
{
    const PER_PAGE_WELCOME = 3;

    public static function getWelcomePerPage()
    {
        return self::PER_PAGE_WELCOME;
    }

    public static function makeWelcomePagination($postsLocale)
    {
        return $pagination = [
            'total' => $postsLocale->total(),
            'lastPage' => $postsLocale->lastPage(),
            'perPage' => $postsLocale->perPage(),
            'currentPage' => $postsLocale->currentPage()
        ];
    }
}