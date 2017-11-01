<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Services\Locale\LocaleSettings;

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
                'id'   => LocaleSettings::createArr['en']['id'],
                'name' => LocaleSettings::createArr['en']['name']
            ],
            [
                'id'   => LocaleSettings::createArr['ru']['id'],
                'name' => LocaleSettings::createArr['ru']['name']
            ],
            [
                'id'   => LocaleSettings::createArr['am']['id'],
                'name' => LocaleSettings::createArr['am']['name']
            ]
        ];

        \App\Locale::insert($createArr);
    }
}
