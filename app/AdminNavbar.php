<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminNavbar extends Model
{
    protected $table = 'admin_navbar';

    public function navbarParts(){
        return $this->hasMany('App\AdminNavbarParts', 'admin_navbar_id', 'id');
    }
}
