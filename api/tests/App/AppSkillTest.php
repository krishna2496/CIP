<?php
use App\Helpers\Helpers;

class AppSkillTest extends TestCase
{
    /**
     * @test
     *
     * Get skill list
     *
     * @return void
     */
    public function it_should_return_all_skill_list()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/skill', ['token' => $token])
        ->seeJsonStructure([
            "status",
            "data" => [
                "*" => []
            ],
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * No data found for skill 
     *
     * @return void
     */
    public function it_should_return_no_skill_found()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('/app/skill', ['token' => $token])
        ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
    }

    /**
     * @test
     * 
     * Return error for invalid token
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_authorization_token_for_get_skill()
    {
        $token = str_random(50);
        $this->get('/app/user-detail', ['token' => $token])
        ->seeStatusCode(400)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message"
                ]
            ]
        ]);
    }
}
