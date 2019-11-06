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
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                "type" => "Weekend only",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                "type" => "Work Week only",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        ];
    
        foreach ($items as $item) {            
            \DB::table('availability')->insert($item);
        }
    }
}
