<?php
use Illuminate\Support\Facades\DB;
use App\Helpers\Helpers;

class MessagesTest extends TestCase
{
    /**
     * @test
     *
     * It should send message to user
     *
     * @return void
     */
    public function it_should_send_message_to_app_user()
    {
        // Create user
        $connection = 'tenant';
        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        $params = [
            "subject" => str_random('50'),
            "message" => str_random('1000'),
            "admin"  => str_random('10'),
            "user_ids" => [
                $user->user_id
            ]
        ];

        DB::setDefaultConnection('mysql');
        // Add messages from admin side
        $response = $this->post('message/send', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        DB::setDefaultConnection('mysql');
        // Fetch those created message by admin
        $response = $this->get('app/messages', ['token' => $token])
        ->seeStatusCode(200);

        // Fetch all messages, sent from admin
        $messages = json_decode($response->response->getContent())->data->message_data;
        
        for ($i=0; $i<count($messages); $i++)
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
     * It should return validation error for subject is required on send message to user
     *
     * @return void
     */
    public function it_should_return_validation_error_for_subject_is_required_on_send_message_to_user()
    {
        // Create user
        $connection = 'tenant';
        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        $params = [
            "subject" => '',
            "message" => str_random('1000'),
            "admin"  => str_random('10'),
            "user_ids" => [
                $user->user_id
            ]
        ];

        // Send message to user
        $response = $this->post('message/send', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        $user->delete();
    }

    /**
     * @test
     *
     * It should return validation error for invalie user ids on send message to user
     *
     * @return void
     */
    public function it_should_return_validation_error_for_invalid_user_ids_on_send_message_to_user()
    {
        $params = [
            "subject" => str_random('50'),
            "message" => str_random('1000'),
            "admin"  => str_random('10'),
            "user_ids" => [
               rand(9000000000,90000000000),
               rand(9000000000,90000000000)
            ]
        ];

        // Send message to user
        $response = $this->post('message/send', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
    }

     /**
     * @test
     *
     * It should return validation error for user ids must be integer on send message to user
     *
     * @return void
     */
    public function it_should_return_validation_error_for_user_ids_must_be_int_on_send_message_to_user()
    {
        $params = [
            "subject" => str_random('50'),
            "message" => str_random('1000'),
            "admin"  => str_random('10'),
            "user_ids" => [
               str_random('5'),
               str_random('5')
            ]
        ];

        // Send message to user
        $response = $this->post('message/send', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
    }

    /**
     * @test
     *
     * It should delete user's messages
     *
     * @return void
     */
    public function it_should_delete_user_messages()
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
     * It should return error message id not found on delete user's messages
     *
     * @return void
     */
    public function it_should_return_error_message_id_not_found_on_delete_user_messages()
    {
        $messageId = rand(9000000000,90000000000);
        $response = $this->delete('message/'.$messageId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);        
    }

    /**
     * @test
     *
     * It should return error message id not found on delete user's messages
     *
     * @return void
     */
    public function it_should_fetch_all_messages_sent_from_user()
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

        $this->get('message/list', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        
        \DB::setDefaultConnection('mysql');
        $response = $this->delete('message/'.$messageId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

        $user->delete();
    }

    /**
     * @test
     *
     * It should return error message id not found on delete user's messages
     *
     * @return void
     */
    public function it_should_fetch_all_messages_sent_from_multiple_users()
    {
        // First user create and send message to admin
        $connection = 'tenant';
        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'subject' => str_random('50'),
            'message' => str_random('100')
        ];
        $userId1 = $user->user_id;

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
        
        $messageId1 = (json_decode($response->response->getContent())->data->message_id)[0];
                
        // Second user create and send message to admin
        \DB::setDefaultConnection('mysql');
        $connection = 'tenant';
        
        $user2 = factory(\App\User::class)->make();
        $user2->setConnection($connection);
        $user2->save();

        $params2 = [
            'subject' => str_random('50'),
            'message' => str_random('100')
        ];

        $userId2 = $user2->user_id;
        
        $token2 = Helpers::getJwtToken($user2->user_id, env('DEFAULT_TENANT'));
        
        $response = $this->post('app/message/send', $params2, ['token' => $token2])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "data" => [
                "message_id"
            ],
            "message"
        ]);
        
        $messageId2 = (json_decode($response->response->getContent())->data->message_id)[0];

        \DB::setDefaultConnection('mysql');

        $this->get("message/list?users=$userId1,$userId2", ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);
        
        \DB::setDefaultConnection('mysql');
        $response = $this->delete('message/'.$messageId1, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

        \DB::setDefaultConnection('mysql');
        $response = $this->delete('message/'.$messageId2, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

        $user->delete();
    }

    /**
     * @test
     *
     * It should return no message found from user
     *
     * @return void
     */
    public function it_should_return_no_message_found_from_users()
    {
        // First user create and send message to admin
        $connection = 'tenant';
        
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            'subject' => str_random('50'),
            'message' => str_random('100')
        ];
        
        $response = $this->get("message/list?users=$user->user_id", ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'message',
            'status'
        ]);
    }

    /**
     * @test
     *
     * It should read message sent from user
     *
     * @return void
     */
    public function it_should_read_message_sent_from_user()
    {
        // Uuser create and send message to admin
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
        $this->post("message/read/$messageId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'data' => [
                'message_id'
            ],
            'message'
        ]);

        \DB::setDefaultConnection('mysql');
        $response = $this->delete('message/'.$messageId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);

        $user->delete();
    }

    /**
     * @test
     *
     * It should return error message_id not found on read message sent from user
     *
     * @return void
     */
    public function it_should_return_error_message_id_not_found_on_read_message_sent_from_user()
    {
        $messageId = rand(8000000000,80000000000);
        $this->post("message/read/$messageId", [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * It should send message to multiple users
     *
     * @return void
     */
    public function it_should_send_messages_to_multiple_users()
    {
        // Create user
        $connection = 'tenant';

        // First user created
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));

        $userId1 = $user->user_id;

        // Second user created
        $user2 = factory(\App\User::class)->make();
        $user2->setConnection($connection);
        $user2->save();
        
        $token2 = Helpers::getJwtToken($user2->user_id, env('DEFAULT_TENANT'));

        $userId2 = $user2->user_id;

        $params = [
            "subject" => str_random('50'),
            "message" => str_random('1000'),
            "admin"  => str_random('10'),
            "user_ids" => [
                $userId1,$userId2
            ]
        ];

        DB::setDefaultConnection('mysql');
        // Add messages from admin side
        $response = $this->post('message/send', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);


        // First user's fetch created message by admin
        DB::setDefaultConnection('mysql');
        
        $response = $this->get('app/messages', ['token' => $token])
        ->seeStatusCode(200);

        // Fetch all messages, sent from admin
        $messages = json_decode($response->response->getContent())->data->message_data;
        
        for ($i=0; $i<count($messages); $i++)
        {
            DB::setDefaultConnection('mysql');
            $message = $messages[$i];
            // Delete message from database
            $this->delete('app/message/'.$message->message_id, [], ['token' => $token])
            ->seeStatusCode(204);
        }

        // Second user's fetch created message by admin
        DB::setDefaultConnection('mysql');
        
        $response = $this->get('app/messages', ['token' => $token2])
        ->seeStatusCode(200);

        // Fetch all messages, sent from admin
        $messages = json_decode($response->response->getContent())->data->message_data;
        
        for ($i=0; $i<count($messages); $i++)
        {
            DB::setDefaultConnection('mysql');
            $message = $messages[$i];
            // Delete message from database
            $this->delete('app/message/'.$message->message_id, [], ['token' => $token2])
            ->seeStatusCode(204);
        }
        
        $user->delete();
        $user2->delete();
    }
}
