<?php

use App\Helpers\Helpers;

class CommentsTest extends TestCase
{
    /**
     * @test
     *
     * Get all mission related comments by mission id
     *
     * @return void
     */
    public function it_should_return_comments_listing_by_mission_id()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "comment" => str_random('100'),
            "mission_id" => $mission->mission_id
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('/app/mission/comment', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        App\Models\Comment::where('user_id', $user->user_id)->update(['approval_status' => 'PUBLISHED']);
        DB::setDefaultConnection('mysql');
        
        $this->get('/missions/'.$mission->mission_id.'/comments', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message",
            "data" => [
                "*" => [
                    "comment_id",
                    "comment",
                    "approval_status",
                    "created_at",
                    "user" => [
                        "user_id",
                        "first_name",
                        "last_name",
                        "avatar"
                    ]
                ]
            ]
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * It should return error for no comments found by mission id
     *
     * @return void
     */
    public function it_should_return_no_comments_found_for_mission()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $this->get('/missions/'.$mission->mission_id.'/comments', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid mission id
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_mission_id_for_get_comments_listing()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        $missionId = rand(1000000,2000000);
        
        $token = Helpers::getJwtToken($user->user_id);
        $this->get('/missions/'.$missionId.'/comments', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]);  
        $user->delete();
    }
    
    /**
     * @test
     *
     * Get comment detail by comment id
     *
     * @return void
     */
    public function it_should_return_comment_detail_by_comment_id()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "comment" => str_random('100'),
            "mission_id" => $mission->mission_id
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('/app/mission/comment', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        App\Models\Comment::where('user_id', $user->user_id)->update(['approval_status' => config("constants.comment_approval_status.PUBLISHED")]);
        $comment = App\Models\Comment::where('user_id', $user->user_id)->first();
       
        DB::setDefaultConnection('mysql');
        
        $this->get('/missions/'.$mission->mission_id.'/comments/'.$comment->comment_id, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message",
            "data" => [
                    "comment_id",
                    "comment",
                    "approval_status",
                    "created_at"
            ]
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid comment id
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_coment_id_for_get_a_comment()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();
        
        $this->get('/missions/'.$mission->mission_id.'/comments/'.rand(1000000, 2000000), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid mission id
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_mission_id_for_comment_detail_by_comment_id()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "comment" => str_random('100'),
            "mission_id" => $mission->mission_id
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('/app/mission/comment', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        App\Models\Comment::where('user_id', $user->user_id)->update(['approval_status' => config("constants.comment_approval_status.PUBLISHED")]);
        $comment = App\Models\Comment::where('user_id', $user->user_id)->first();
       
        DB::setDefaultConnection('mysql');
        
        $this->get('/missions/'.rand(1000000, 2000000).'/comments/'.$comment->comment_id, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * It should update comment status
     *
     * @return void
     */
    public function it_should_update_comment()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "comment" => str_random('100'),
            "mission_id" => $mission->mission_id
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('/app/mission/comment', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $comment = App\Models\Comment::where('user_id', $user->user_id)->first();
       
        DB::setDefaultConnection('mysql');
        
        $params = [
            "approval_status" => config("constants.comment_approval_status.PUBLISHED"),
        ];

        $this->patch('/missions/'.$mission->mission_id.'/comments/'.$comment->comment_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "message"
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid comment id
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_comment_id()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "comment" => str_random('100'),
            "mission_id" => $mission->mission_id
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('/app/mission/comment', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $comment = App\Models\Comment::where('user_id', $user->user_id)->first();
       
        DB::setDefaultConnection('mysql');
        
        $params = [
            "approval_status" => config("constants.comment_approval_status.PUBLISHED"),
        ];

        $this->patch('/missions/'.$mission->mission_id.'/comments/'.rand(1000000, 2000000), $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * It should return error for invalid status
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_comment_status()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "comment" => str_random('100'),
            "mission_id" => $mission->mission_id
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('/app/mission/comment', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $comment = App\Models\Comment::where('user_id', $user->user_id)->first();
       
        DB::setDefaultConnection('mysql');
        
        $params = [
            "approval_status" => '',
        ];

        $this->patch('/missions/'.$mission->mission_id.'/comments/'.$comment->comment_id, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422)
        ->seeJsonStructure([
            'errors' => [
                [
                    'status',
                    'type',
                    'code',
                    'message'
                ]
            ]
        ]);
        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * Remove comment for mission
     *
     * @return void
     */
    public function it_should_remove_comment()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "comment" => str_random('100'),
            "mission_id" => $mission->mission_id
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('/app/mission/comment', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $comment = App\Models\Comment::where('user_id', $user->user_id)->first();
       
        DB::setDefaultConnection('mysql');
        
        $this->delete(
            '/missions/'.$mission->mission_id.'/comments/'.$comment->comment_id,
            [],
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
        ->seeStatusCode(204);

        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * Return error for invalid comment id for remove comment for mission
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_comment_id_for_remove_comment()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $this->delete(
            '/missions/'.$mission->mission_id.'/comments/'.rand(1000000, 2000000),
            [],
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]); 

        $user->delete();
        $mission->delete();
    }

    /**
     * @test
     *
     * Return error for invalid mission id for remove comment for mission
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_mission_id_for_remove_comment()
    {
        $connection = 'tenant';
        $mission = factory(\App\Models\Mission::class)->make();
        $mission->setConnection($connection);
        $mission->save();

        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $params = [
            "comment" => str_random('100'),
            "mission_id" => $mission->mission_id
        ];

        $token = Helpers::getJwtToken($user->user_id);
        $this->post('/app/mission/comment', $params, ['token' => $token])
          ->seeStatusCode(201)
          ->seeJsonStructure([
            "status",
            "message"
        ]);
        $comment = App\Models\Comment::where('user_id', $user->user_id)->first();
       
        DB::setDefaultConnection('mysql');
        
        $this->delete(
            '/missions/'.rand(1000000, 2000000).'/comments/'.$comment->comment_id,
            [],
            ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))]
        )
        ->seeStatusCode(404)
        ->seeJsonStructure([
            "errors" => [
                [
                    "status",
                    "type",
                    "message",
                    "code"
                ]
            ]
        ]); 
        
        $user->delete();
        $mission->delete();
    }    
}
