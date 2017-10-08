<?php

use Illuminate\Database\Seeder;

class FillAdminNavbarPartsSeeder extends Seeder
{
    private static $table = 'admin_navbar_parts';

    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table(self::$table)->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $postNav = \App\AdminNavbar::where('alias', 'posts')->first();
        $insertArrPost = [
            [
                'alias' => 'crud',
                'name' => 'crud posts',
            ],
            [
                'alias' => 'hashtag',
                'name' => 'hashtag',
            ]
        ];
        $postNav->navbarParts()->createMany($insertArrPost);

        $categNav = \App\AdminNavbar::where('alias', 'categories')->first();
        $insertArrCateg = [
            [
                'alias' => 'categories',
                'name' => 'categories',
            ],
            [
                'alias' => 'subcategories',
                'name' => 'subcategories',
            ],
        ];
        $categNav->navbarParts()->createMany($insertArrCateg);
    }
}
