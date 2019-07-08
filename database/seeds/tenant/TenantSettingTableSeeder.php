<?php

use Illuminate\Database\Seeder;

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
                'value' => '1'
            ],
            [
                'title' => 'Sorting missions',
                'description' => 'testing description here',
                'key' => 'sorting_missions',
                'value' => '1'
            ],
            [
                'title' => 'Total Hours Volunteered In The Platform',
                'description' => 'testing description here',
                'key' => 'total_hours_volunteered',
                'value' => '1'
            ],
            [
                'title' => 'Total Votes In The Platform',
                'description' => 'testing description here',
                'key' => 'total_votes',
                'value' => '1'
            ],
            [
                'title' => 'Total Missions In The Platform',
                'description' => 'testing description here',
                'key' => 'total_missions',
                'value' => '1'
            ],
        ];
    
        foreach ($items as $item) {            
            \DB::table('tenant_setting')->insert($item);
        }
    }
}
