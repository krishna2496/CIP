<?php

namespace Tests\Unit\Http\Controllers\Admin\Mission;

use App\Models\Mission;
use TestCase;

class MissionControllerTest extends TestCase
{
    /**
     * @testdox Test  store donation mission validation failed
     *
     * @return void
     */
    public function testStoreMission_001()
    {
    }

    /**
     * @testdox Test store donation mission success
     *
     * @return void
     */
    public function testStoreMission_002()
    {
        $connection = 'tenant';
        $mission = factory(Mission::class)->make();
        dd($mission['donationAttribute']);
        $mission->mission_type = config('constants.mission_type.DONATION');
        $mission->donation_attribute['goal_amount_currency'] = 'USD';
        $mission->donation_attribute['goal_amount'] = 23;
        $mission->donation_attribute['show_goal_amount'] = 3;
        $mission->donation_attribute['show_donation_percentage'] = 0;
        $mission->donation_attribute['show_donation_meter'] = 0;
        $mission->donation_attribute['show_donation_count'] = 0;
        $mission->donation_attribute['show_donors_count'] = 0;
        $mission->donation_attribute['disable_when_funded'] = 0;
        $mission->donation_attribute['is_disabled'] = 0;

        dd($mission->toArray());
        // $user->setConnection($connection);
        // $user->save();
    }
}
