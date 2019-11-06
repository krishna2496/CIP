<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NotificationTypeTableSeeder extends Seeder
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
                "notification_type" => "Recommended missions",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                "notification_type" => "Volunteering hours",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                "notification_type" => "Volunteering goals",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                "notification_type" => "My comments",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                "notification_type" => "My stories",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                "notification_type" => "New stories hours",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                "notification_type" => "New missions",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [                
                "notification_type" => "New messages",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
           
        ];
    
        foreach ($items as $item) {            
            \DB::table('notification_type')->insert($item);
        }
    }
}
