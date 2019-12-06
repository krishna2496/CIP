<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AvailabilityTableSeeder extends Seeder
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
                "type" => "Anytime",
                "translations" => 'a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:7:"Anytime";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:10:"Anytime fr";}}',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                "type" => "Weekend only",
                "translations" => 'a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:12:"Weekend only";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:15:"Fr Weekend only";}}',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                "type" => "Work Week only",
                "translations" => 'a:2:{i:0;a:2:{s:4:"lang";s:2:"en";s:5:"title";s:14:"Work Week only";}i:1;a:2:{s:4:"lang";s:2:"fr";s:5:"title";s:17:"Fr Work Week only";}}',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        ];
    
        foreach ($items as $item) {            
            \DB::table('availability')->insert($item);
        }
    }
}
