<?php
use Illuminate\Support\Facades\DB;
use App\Helpers\Helpers;

class AppMessagesTest extends TestCase
{
    /**
     * @test
     *
     * It should send message to admin
     *
     * @return void
     */
    public function it_should_send_message_to_admin()
    {
        $connection = 'tenant';
        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'subject' => str_random('50'),
            'message' => str_random('100')
        ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        
        $response = $this->post('app/message/send', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "data" => [
                "message_id"
            ],
            "message"
        ]);
        
        $messageId = (json_decode($response->response->getContent())->data->message_id)[0];
        
        
        \DB::setDefaultConnection('mysql');
        $response = $this->delete('app/message/'.$messageId, [], ['token' => $token]);
        
        $this->seeStatusCode(204);

        $user->delete();
    }

    /**
     * @test
     *
     * It should return validation error for subject required on send message to admin
     *
     * @return void
     */
    public function it_should_return_validation_error_for_subject_required_on_send_message_to_admin()
    {
        $connection = 'tenant';
        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        // Subject is required field to send message
        $params = [
            'subject' => '',
            'message' => str_random('100')
        ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        
        $this->post('app/message/send', $params, ['token' => $token])->seeStatusCode(422);
        $user->delete();
    }

    /**
     * @test
     *
     * It should return validation error for subject max char limit on send message to admin
     *
     * @return void
     */
    public function it_should_return_validation_error_for_subject_max_char_limit_on_send_message_to_admin()
    {
        $connection = 'tenant';
        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        // Subject field allow max 255 characters
        $params = [
            'subject' => str_random('500'),
            'message' => str_random('100')
        ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        
        $this->post('app/message/send', $params, ['token' => $token])->seeStatusCode(422);
        $user->delete();
    }

    /**
     * @test
     *
     * It should return validation error for message required on send message to admin
     *
     * @return void
     */
    public function it_should_return_validation_error_for_message_required_on_send_message_to_admin()
    {
        $connection = 'tenant';
        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        // Message is required field to send message
        $params = [
            'subject' => str_random('50'),
            'message' => ''
        ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        
        $this->post('app/message/send', $params, ['token' => $token])->seeStatusCode(422);
        $user->delete();
    }

    /**
     * @test
     *
     * It should return validation error for message max char limit on send message to admin
     *
     * @return void
     */
    public function it_should_return_validation_error_for_message_max_char_limit_on_send_message_to_admin()
    {
        $connection = 'tenant';
        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        // Message field allow max 1000 characters
        $params = [
            'subject' => str_random('50'),
            'message' => str_random('60001')
        ];

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        
        $this->post('app/message/send', $params, ['token' => $token])->seeStatusCode(422);
        $user->delete();
    }
}
