<?php

namespace App\Http\Controllers\Helpers;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;

class Helpers extends Controller
{
    /**
     * Explodes Request Alias, Get Last part, which is ID, and return POST by ID
     * @param $postAlias
     * @return null
     */
    public static function getPostByRequest($postAlias)
    {
        $post_id = self::explodeGetLast($postAlias);

        if ($post = Post::findOrFail($post_id)) {
            return $post;
        } else {
            return null;
        }
    }

    // ToDo Should here add CACHE part logic. Change , when PC will be 64 ))
    public static function prepareAdminNavbars($part)
    {
        $response = [];
        $adminController = new AdminController();
        $response['leftNav'] = $adminController->getLeftNavbar();
        $response['panel'] = $adminController->getPanelNavbar($part);
        return $response;
    }

    /**
     * Just get string with '_', explode and return last
     * @param $string
     * @return mixed
     */
    public static function explodeGetLast($string) {
        $expl_post = explode('_', $string);
        return $expl_post[count($expl_post)-1];
    }
}
