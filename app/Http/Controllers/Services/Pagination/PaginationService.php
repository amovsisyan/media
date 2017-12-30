<?php
namespace App\Http\Controllers\Services\Pagination;

class PaginationService
{
    const PER_PAGE_WELCOME = 9;

    public static function getWelcomePerPage()
    {
        return self::PER_PAGE_WELCOME;
    }

    public static function makeWelcomePagination($postsLocale)
    {
        $pagination = [];
        if ($total = $postsLocale->total() > self::PER_PAGE_WELCOME) {
            $pagination = [
                'total' => $total,
                'lastPage' => $postsLocale->lastPage(),
                'perPage' => $postsLocale->perPage(),
                'currentPage' => $postsLocale->currentPage()
            ];
        }

        return $pagination;
    }
}