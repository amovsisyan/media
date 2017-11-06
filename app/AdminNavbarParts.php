<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminNavbarParts extends Model
{
    protected $table = 'admin_navbar_parts';

    public function navbar(){
        return $this->belongsTo('App\AdminNavbar', 'admin_navbar_part_id');
    }

    public function panelParts(){
        return $this->hasMany('App\AdminPanelNavbar', 'admin_navbar_part_id', 'id');
    }

    public static function preparePanelNavbar($part = null)
    {
        $res = [];
        if ($part !== null) {
            $part = self::where('alias', $part)->first();
            // todo change into with
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
