<?php
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Mission;
use Illuminate\Support\Facades\Config;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class MissionApplicationTest extends TestCase
{
    /**
    * @test
    *
    * Get all mission applications
    *
    * @return void
    */
    // public function it_should_return_mission_applications()
    // {
    //     DB::setDefaultConnection('tenant');
    //     $connection = 'tenant';
    //     $missionApplication = factory(\App\Models\MissionApplication::class)->make();
    //     $missionApplication->setConnection($connection);
    //     $missionApplication->save();
    //     $missionApplication->mission_id = App\Models\Mission::get()->random()->mission_id;
    //     $missionApplication->user_id = App\User::get()->random()->user_id;
    //     $missionApplication->update(); 

    //     $this->get('/missions/'.$missionApplication->mission_id.'/applications', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
    //     ->seeStatusCode(200);
    // }
    
}
