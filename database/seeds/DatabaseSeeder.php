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
        $this->call('CountryTableSeeder');
        $this->call('TimezoneTableSeeder');
        $this->call('CityTableSeeder');
        $this->call('MissionThemeTableSeeder');
        $this->call('NotificationTypeTableSeeder');
        $this->call('AvailabilityTableSeeder');
    }
}
