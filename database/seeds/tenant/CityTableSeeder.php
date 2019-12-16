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
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 233,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 21,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 21,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 21,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 21,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 21,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                'country_id' => 78,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
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
