<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class Helpers extends Controller
{
    public static function getPostByRequest($postAlias)
    {
        $expl_post = explode('_', $postAlias);
        $post_id = $expl_post[count($expl_post)-1];

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
}
