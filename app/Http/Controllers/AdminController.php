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
        $response['leftNav'] = $this->getLeftNavbar();

        return response()
            -> view('admin.index', ['response' => $response]);
    }

    public function part(Request $request, $navbar, $part)
    {
        $response['leftNav'] = $this->getLeftNavbar();
        $response['panel'] = $this->getPanelNavbar($part);

        return response()
            -> view('admin.index', ['response' => $response]);
    }

    protected function getLeftNavbar()
    {
        $navbar = AdminNavbar::select('id', 'alias', 'name')->get();

        $res = [];
        foreach ($navbar as $key => $nav) {
            $res[$key]['nav'] = [
                'id'        => $nav->id,
                'alias'     => $nav->alias,
                'name'      => $nav->name,
            ];
            $navbar_parts = $nav->navbarParts()->get();
            foreach ($navbar_parts as $parts) {
                $res[$key]['part'][] = [
                    'id'        => $parts->id,
                    'alias'     => $parts->alias,
                    'name'      => $parts->name,
                ];
            }
        }

        return $res;
    }

    protected function getPanelNavbar($part = null)
    {
        $res = [];
        if ($part !== null) {
            $part = AdminNavbarParts::where('alias', $part)->first();
            $panel_navbar = $part->panelParts()->get();
            foreach ($panel_navbar as $key => $parts) {
                $res[] = [
                    'id'        => $parts->id,
                    'alias'     => $parts->alias,
                    'name'      => $parts->name,
                ];

            }
        }

        return $res;
    }
}
