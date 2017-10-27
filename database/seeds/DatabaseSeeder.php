<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(FillAdminNavbarSeeder::class);
         $this->call(FillAdminNavbarPartsSeeder::class);
         $this->call(FillAdminPanelNavbarSeeder::class);
         $this->call(FillLocale::class);
    }
}
