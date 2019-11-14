<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TimesheetStatusTableSeeder extends Seeder
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
                "status" => "PENDING",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                "status" => "APPROVED",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                "status" => "DECLINED",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                "status" => "AUTOMATICALLY_APPROVED",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                "status" => "SUBMIT_FOR_APPROVAL",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        ];
    
        foreach ($items as $item) {            
            \DB::table('timesheet_status')->insert($item);
        }
    }
}
