<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TenantSettingTableSeeder extends Seeder
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
                'title' => 'Quick access Filters',
                'description' => 'testing description here',
                'key' => 'quick_access_filters',
                'value' => '1',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'title' => 'Sorting missions',
                'description' => 'testing description here',
                'key' => 'sorting_missions',
                'value' => '1',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'title' => 'Total Hours Volunteered In The Platform',
                'description' => 'testing description here',
                'key' => 'total_hours_volunteered',
                'value' => '1',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'title' => 'Total Votes In The Platform',
                'description' => 'testing description here',
                'key' => 'total_votes',
                'value' => '1',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                'title' => 'Total Missions In The Platform',
                'description' => 'testing description here',
                'key' => 'total_missions',
                'value' => '1',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
        ];
    
        foreach ($items as $item) {            
            \DB::table('tenant_setting')->insert($item);
        }
    }
}
