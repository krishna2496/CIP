<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MissionThemeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [            
            [
                'theme_name' => 'Environment',
                'translations' => 'a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:11:"Environment";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:13:"Environnement";}}',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        ];
    
        foreach ($items as $item) {            
            \DB::table('mission_theme')->insert($item);
        }
    }
}
