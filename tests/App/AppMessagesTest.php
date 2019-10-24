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
        $response = $this->delete('message/'.$messageId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

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

    /**
     * @test
     * 
     * This case will cover three things together
     * Admin : Send message to user
     * User : Get messages list which sent by admin
     * User : Delete messages of user sent by admin
     * 
     * @return void
     */
    public function it_should_send_message_to_user_and_get_list_and_delete_messages_from_admin()
    {
        // Create user
        $connection = 'tenant';
        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        for ($i=0; $i<5; $i++)
        {
            DB::setDefaultConnection('mysql');
            $params = [
                "subject" => str_random('50'),
                "message" => str_random('1000'),
                "admin"  => str_random('10'),
                "user_ids" => [
                    $user->user_id
                ]
            ];
            // Add messages from admin side
            $response = $this->post('message/send', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
            ->seeStatusCode(201);
        }        
        DB::setDefaultConnection('mysql');
        // Fetch those created message by admin
        $response = $this->get('app/messages', ['token' => $token])
        ->seeStatusCode(200);

        // Fetch all messages, sent from admin
        $messages = json_decode($response->response->getContent())->data->message_data;
        
        for ($i=0; $i<5; $i++)
        {
            DB::setDefaultConnection('mysql');
            $message = $messages[$i];
            // Delete message from database
            $this->delete('app/message/'.$message->message_id, [], ['token' => $token])
            ->seeStatusCode(204);
        }
        $user->delete();
    }

    /**
     * @test
     * 
     * It should empty list messages from admin
     * 
     * @return void
     */
    public function it_should_return_empty_list_messages_from_admin()
    {
        // Create user
        $connection = 'tenant';
        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        // Fetch those created message by admin
        $response = $this->get('app/messages', ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);

        $user->delete();
    }

    /**
     * @test
     * 
     * It should return error, message not found on delete messages from admin
     * 
     * @return void
     */
    public function it_should_return_error_message_not_found_on_delete_messages_from_admin()
    {
        // Create user
        $connection = 'tenant';
        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        $messageId = rand(50000000000000,500000000000000);

        $this->delete('app/message/'.$messageId, [], ['token' => $token])
        ->seeStatusCode(404);

        $user->delete();
    }
    
    /**
     * @test
     * 
     * It should read message sent from admin
     * 
     * @return void
     */
    public function it_should_read_message_sent_from_admin()
    {
        // Create user
        $connection = 'tenant';
        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        for ($i=0; $i<5; $i++)
        {
            DB::setDefaultConnection('mysql');
            $params = [
                "subject" => str_random('50'),
                "message" => str_random('1000'),
                "admin"  => str_random('10'),
                "user_ids" => [
                    $user->user_id
                ]
            ];
            // Add messages from admin side
            $response = $this->post('message/send', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
            ->seeStatusCode(201);
        }

        DB::setDefaultConnection('mysql');
        // Fetch those created message by admin
        $response = $this->get('app/messages', ['token' => $token])
        ->seeStatusCode(200);

        // Fetch all messages, sent from admin
        $messages = json_decode($response->response->getContent())->data->message_data;
        
        for ($i=0; $i<5; $i++)
        {
            DB::setDefaultConnection('mysql');
            $message = $messages[$i];
            // Read message sent from admin
            $this->post('app/message/read/'.$message->message_id, [], ['token' => $token])
            ->seeStatusCode(200)
            ->seeJsonStructure([
                'message',
                'status',
                'data' => [
                    'message_id'
                ]
            ]);
            DB::setDefaultConnection('mysql');
            // Delete message from database
            $this->delete('app/message/'.$message->message_id, [], ['token' => $token])
            ->seeStatusCode(204);
        }
        $user->delete();        
    }

    /**
     * @test
     * 
     * It should read message sent from admin
     * 
     * @return void
     */
    public function it_should_return_error_message_not_found_on_read_message_sent_from_admin()
    {
        // Create user
        $connection = 'tenant';
        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        $messageId = rand(50000000000000,500000000000000);
        $this->post('app/message/read/'.$messageId, [], ['token' => $token])
        ->seeStatusCode(404);

        $user->delete();
    }
}
