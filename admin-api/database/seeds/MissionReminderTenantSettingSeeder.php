<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MissionReminderTenantSettingSeeder extends Seeder
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
                'title' => 'Mission reminder',
                'description' => 'Enable/disable mission reminder on platform',
                'key' => 'mission_reminder',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        ];

        foreach ($items as $item) {            
            \DB::table('tenant_setting')->insert($item);
        }
    }
}
