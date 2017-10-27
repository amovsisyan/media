<?php

use Illuminate\Database\Seeder;

class FillLocale extends Seeder
{
    private static $table = 'locale';

    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table(self::$table)->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $createArr = [
            [
                'id' => 1,
                'name' => 'en'
            ],
            [
                'id' => 2,
                'name' => 'ru'
            ],
            [
                'id' => 3,
                'name' => 'am'
            ]
        ];

        \App\Locale::insert($createArr);
    }
}
