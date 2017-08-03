<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminPanelNavbar extends Model
{
    protected $table = 'admin_panel_navbar';

    public function navbarPart(){
        return $this->belongsTo('App\AdminNavbarParts', 'admin_navbar_id');
    }
}
