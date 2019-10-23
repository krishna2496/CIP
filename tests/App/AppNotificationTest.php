<?php
use App\Helpers\Helpers;

class AppNotificationTest extends TestCase
{
    /**
     * @test
     *
     * Get notification settings
     *
     * @return void
     */
    public function it_should_return_notification_settings()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->get('app/notification-settings', ['token' => $token])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
    }
}
