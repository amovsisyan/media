<?php

namespace App\Http\Controllers\Admin\Categories;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoriesController extends MainCategoriesController
{
    protected function change()
    {
        return response()
            -> view('admin.categories.categories.change');
    }
}
