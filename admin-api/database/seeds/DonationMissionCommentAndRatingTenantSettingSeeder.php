<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DonationMissionCommentAndRatingTenantSettingSeeder extends Seeder
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
                'title' => 'Donation mission comments',
                'description' => 'Donation mission commments is enabled/disabled',
                'key' => 'donation_mission_comments',
                "created_at" => Carbon::now()
            ],
            [
                'title' => 'Donation mission ratings',
                'description' => 'Donation mission ratings is enabled/disabled',
                'key' => 'donation_mission_ratings',
                "created_at" => Carbon::now()
            ],

        ];

        foreach ($items as $item) {
            \DB::table('tenant_setting')->insert($item);
        }
    }
}
