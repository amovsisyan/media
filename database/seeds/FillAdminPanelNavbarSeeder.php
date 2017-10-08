<?php

use Illuminate\Database\Seeder;

class FillAdminPanelNavbarSeeder extends Seeder
{
    private static $table = 'admin_panel_navbar';

    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table(self::$table)->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $postPartCrud = \App\AdminNavbarParts::where('alias', 'crud')->first();
        $insertArrCrud = [
            [
                'alias' => 'create_post',
                'name' => 'create post',
            ],
            [
                'alias' => 'update_post',
                'name' => 'update post',
            ]
        ];
        $postPartCrud->panelParts()->createMany($insertArrCrud);

        $postPartHashtag = \App\AdminNavbarParts::where('alias', 'hashtag')->first();
        $insertArrHashtag = [
            [
                'alias' => 'edit_hashtag',
                'name' => 'edit hashtag',
            ],
            [
                'alias' => 'create_hashtag',
                'name' => 'create hashtag',
            ]
        ];
        $postPartHashtag->panelParts()->createMany($insertArrHashtag);

        $postPartCategories = \App\AdminNavbarParts::where('alias', 'categories')->first();
        $insertArrCategories = [
            [
                'alias' => 'create_category',
                'name' => 'create category',
            ],
            [
                'alias' => 'delete_category',
                'name' => 'delete category',
            ],
            [
            'alias' => 'edit_category',
            'name' => 'edit category',
            ]
        ];
        $postPartCategories->panelParts()->createMany($insertArrCategories);

        $postPartSubcategories = \App\AdminNavbarParts::where('alias', 'subcategories')->first();
        $insertArrSubcategories = [
            [
                'alias' => 'create_subcategory',
                'name' => 'create subcategory',
            ],
            [
                'alias' => 'delete_subcategory',
                'name' => 'delete subcategory',
            ],
            [
                'alias' => 'edit_subcategory',
                'name' => 'edit subcategory',
            ]
        ];
        $postPartSubcategories->panelParts()->createMany($insertArrSubcategories);
    }
}
