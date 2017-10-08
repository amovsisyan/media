<?php

use Illuminate\Database\Seeder;

class FillAdminNavbarSeeder extends Seeder
{
    private static $table = 'admin_navbar';

    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table(self::$table)->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $insertArr = [
            [
                'alias' => 'posts',
                'name' => 'posts',
            ],
            [
                'alias' => 'categories',
                'name' => 'categories',
            ],
        ];
        DB::table(self::$table)->insert($insertArr);
    }
}
