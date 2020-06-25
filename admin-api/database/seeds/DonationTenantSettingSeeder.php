<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DonationTenantSettingSeeder extends Seeder
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
                'title' => 'Donation mission',
                'description' => 'Enable/disable donation on platform',
                'key' => 'donation',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        ];

        foreach ($items as $item) {            
            \DB::table('tenant_setting')->insert($item);
        }
    }
}
