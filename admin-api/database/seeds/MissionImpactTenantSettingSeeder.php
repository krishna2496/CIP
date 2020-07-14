<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MissionImpactTenantSettingSeeder extends Seeder
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
                'title' => 'Impact donation',
                'description' => 'Impact donation is enabled/disabled',
                'key' => 'impact_donation',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        ];

        foreach ($items as $item) {
            \DB::table('tenant_setting')->insert($item);
        }
    }
}
