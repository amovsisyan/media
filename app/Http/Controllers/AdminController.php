<?php

namespace App\Http\Controllers;

use App\AdminNavbar;
use App\AdminNavbarParts;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }


    public function index()
    {
        $response['leftNav'] = AdminNavbar::prepareLeftNavbar();

        return response()
            -> view('admin.index', ['response' => $response]);
    }

    public function part(Request $request, $locale, $navbar, $part)
    {
        $response['leftNav'] = AdminNavbar::prepareLeftNavbar();
        $response['panel'] = AdminNavbarParts::preparePanelNavbar($part);

        return response()
            -> view('admin.index', ['response' => $response]);
    }
}
