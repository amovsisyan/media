<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminNavbarParts extends Model
{
    protected $table = 'admin_navbar_parts';

    public function category(){
        return $this->belongsTo('App\AdminNavbar', 'admin_navbar_part_id');
    }

    public function panelParts(){
        return $this->hasMany('App\AdminPanelNavbar', 'admin_navbar_part_id', 'id');
    }
}
