<?php

namespace App\Http\Controllers\Admin\Categories;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubcategoriesController extends MainCategoriesController
{
    protected function change()
    {
        return response()
            -> view('admin.categories.subcategories.change');
    }
}
