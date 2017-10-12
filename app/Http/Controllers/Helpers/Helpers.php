<?php

namespace App\Http\Controllers\Helpers;

use App\AdminNavbar;
use App\AdminNavbarParts;
use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;

class Helpers extends Controller
{
    /**
     * todo do we need this if we explode in front side
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
        $response['leftNav'] = AdminNavbar::prepareLeftNavbar();
        $response['panel'] = AdminNavbarParts::preparePanelNavbar($part);
        return $response;
    }

    /**
     * todo do we need this if we explode in front side
     * Just get string with '_', explode and return last
     * @param $string
     * @return mixed
     */
    public static function explodeGetLast($string) {
        $expl_post = explode('_', $string);
        return $expl_post[count($expl_post)-1];
    }
}
