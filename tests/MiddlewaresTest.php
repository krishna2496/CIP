<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Aws\S3\Exception\S3Exception;
use App\Models\Tenant;

class MiddlewaresTest extends TestCase
{

    /**
     * 
     *
     * It should return an error, when invalid json passed in request
     * @return void
     */
    public function it_should_return_error_for_invalid_json_request()
    {        
        //
    }

    /**
     * @test
     *
     * It should apply max pagination number, If passed perPage parameter with more number then default max page number.
     * @return void
     */
    public function it_should_apply_max_pagination_number()
    {        
        $response = $this->get("tenants?perPage=8000")
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'data' =>[ 
                '*' => [	
                    'name',
                    'sponsor_id',
                    'status',
                    'tenant_languages' => [
                        '*' => [
                            'language_id',
                            'default'
                        ]
                    ]
                ]
			],
            'pagination' => [
                'total',
                'per_page',
                'current_page',
                'total_pages',
                'next_url',
            ]
        ]);        
        $this->assertEquals(
            json_decode($this->response->getContent())->pagination->per_page,
            config('constants.PER_PAGE_MAX')
        );
    }
}
