<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class VolunteerTenantSettingSeeder extends Seeder
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
                'title' => 'volunteering',
                'description' => 'Volunteering selection is enabled/disabled',
                'key' => 'volunteering',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        ];

        foreach ($items as $item) {            
            \DB::table('tenant_setting')->insert($item);
        }
    }
}
