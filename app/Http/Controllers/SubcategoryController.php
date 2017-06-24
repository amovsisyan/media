<?php

namespace App\Http\Controllers;

use App\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends CategoryController
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getSubCategory (Request $request, $category, $subcategory)
    {
        $expl_subcat = explode('_', $subcategory);
        $sub_cat_id = $expl_subcat[count($expl_subcat)-1];

        $posts = Subcategory::findOrFail($sub_cat_id)->posts()->get();

        $response = [
            'navbar'    => $this->getNavbar(),
            'posts'     => $posts
        ];

        return response()
            -> view('category', ['response' => $response]);
    }
}
