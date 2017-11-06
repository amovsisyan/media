<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminNavbar extends Model
{
    protected $table = 'admin_navbar';

    public function navbarParts(){
        return $this->hasMany('App\AdminNavbarParts', 'admin_navbar_id', 'id');
    }

    public static function prepareLeftNavbar()
    {
        $navbar = self::select('id', 'alias', 'name')->get();

        $res = [];
        foreach ($navbar as $key => $nav) {
            $res[$key]['nav'] = [
                'id'        => $nav->id,
                'alias'     => $nav->alias,
                'name'      => $nav->name,
            ];
            // todo cange into with
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
}
