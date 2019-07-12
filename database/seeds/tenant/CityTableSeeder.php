<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CityTableSeeder extends Seeder
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
                'name' => 'Atlanta', 
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'New York', 
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Los Angeles', 
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Chicago', 
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Houston', 
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'San Diego', 
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Philadelphia', 
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Dallas', 
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'San Jose', 
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Portland', 
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Chiny', 
                'country_id' => 21,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Damme', 
                'country_id' => 21,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Brussels', 
                'country_id' => 21,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Eupen', 
                'country_id' => 21,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Houffalize', 
                'country_id' => 21,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Birmingham', 
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Bristol', 
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Cambridge', 
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Canterbury', 
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Derby', 
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'London', 
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Manchester', 
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'name' => 'Nottingham', 
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        ];

        foreach ($items as $item) {            
            \DB::table('city')->insert($item);
        }
    }
}
