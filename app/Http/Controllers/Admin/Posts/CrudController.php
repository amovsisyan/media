<?php

namespace App\Http\Controllers\Admin\Posts;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CrudController extends PostsController
{
    protected function create()
    {
        return response()
            -> view('admin.posts.crud.create');
    }

    protected function delete()
    {
        return response()
            -> view('admin.posts.crud.delete');
    }

    protected function update()
    {
        return response()
            -> view('admin.posts.crud.update');
    }
}
