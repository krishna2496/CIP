<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Aws\S3\Exception\S3Exception;
use App\Models\ApiUser;
use App\Models\Tenant;

class ApiUserTest extends TestCase
{
    /**
     * @test
     * 
     * Create new api user
     * @return void
     */
    public function it_should_create_api_user()
    {
        // Create tenant
        $tenant = factory(Tenant::class)->create();
        
        // Create api user of create tenant
        $response = $this->post(route("tenants.create-api-user", ["tenant_id" => $tenant->tenant_id]), [])
        ->seeStatusCode(201)
        ->seeJsonStructure([
            'status',
            'data' => [
                'api_user_id',
                'api_key',
                'api_secret',
            ],
            'message',
        ]);

        $apiUserId = $response->response->getData()->data->api_user_id;
        
        // Get api user data from create api user's id
        $apiUser = ApiUser::where('api_user_id', $apiUserId)->first();
        
        // Delete created api user
        $this->assertEquals(true, $apiUser->delete());

        // Delete created tenant
        $this->assertEquals(true, $tenant->delete());        
    }
    
    /**
     * @test
     * 
     * Create api user, while tenant not found
     * @return void
     */
    public function it_should_return_tenant_not_found()
    {
        // Generate random tenantId
        $tenantId = rand(99999999,999999999);
        
        // Create api user of create tenant
        $this->post(route("tenants.create-api-user", ["tenant_id" => $tenantId]), [])
        ->seeStatusCode(404);
    }

    /**
     * @test
     * 
     * List of all api user of tenant
     * @return void
     */
    public function it_should_list_of_all_api_users_of_tenant()
    {
        // Get random tenant
        $tenant = Tenant::get()->random();
        
        // Get all api user of tenant
        $this->get(route("tenants.api-users", ["tenant_id" => $tenant->tenant_id]), [])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "data",
            "pagination",
            "message"
        ]);
    }

    /**
     * @test
     * 
     * List of all api user, while tenant not found
     * @return void
     */
    public function it_should_return_tenant_not_found_for_api_user_listing()
    {
        // Generate random tenantId
        $tenantId = rand(99999999,999999999);
        
        // Get all api users of tenant
        $this->get(route("tenants.api-users", ["tenant_id" => $tenantId]), [])
        ->seeStatusCode(404);
    }

    /**
     * @test
     * 
     * Detail of api user
     * @return void
     */
    public function it_should_return_api_user_detail()
    {
        // Get random tenant with api users data
        $tenant = Tenant::with('apiUsers')->get()->random();

        // Get random api user from list
        $apiUser = $tenant->apiUsers->random();
        
        // Get details of api user, based on tenant_id and api_user_id
        $this->get(route("tenants.get-api-user", ["tenant_id" => $tenant->tenant_id, "api_user_id" => $apiUser->api_user_id]), [])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "data" => [
                "api_user_id",
                "tenant_id",
                "api_key",
                "status"
            ],
            "message"
        ]);
    }

    /**
     * @test
     * 
     * Detail of api user, while tenant not found
     * @return void
     */
    public function it_should_return_tenant_not_found_for_api_user_detail()
    {
        // Generate random tenantId
        $tenantId = rand(99999999,999999999);

        // Get random tenant with api users data
        $tenant = Tenant::with('apiUsers')->get()->random();

        // Get random api user from list
        $apiUser = $tenant->apiUsers->random();
        
        // Get details of api user, based on tenantId and api_user_id
        $this->get(route("tenants.get-api-user", ["tenant_id" => $tenantId, "api_user_id" => $apiUser->api_user_id]), [])
        ->seeStatusCode(404);
    }

    /**
     * @test
     * 
     * Detail of api user, while api user not found for tenant
     * @return void
     */
    public function it_should_return_api_user_not_found_for_api_user_detail()
    {
        // Get random tenant with api users data
        $tenant = Tenant::with('apiUsers')->get()->random();

        // Generate random tenantId
        $apiUserId = rand(99999999,999999999);
    
        // Get details of api user, based on tenantId and api_user_id
        $this->get(route("tenants.get-api-user", ["tenant_id" => $tenant->tenant_id, "api_user_id" => $apiUserId]), [])
        ->seeStatusCode(404);
    }

    /**
     * @test
     * 
     * Renew api user's key
     * @return void
     */
    public function it_should_return_updated_secret_key_of_api_user()
    {
        // Create new tenant
        $tenant = factory(Tenant::class)->create();

        // Create new api user
        $data['api_key'] = str_random(10);
        $data['api_secret'] = str_random(10);

        $apiUser = $tenant->apiUsers()->create($data);        

        // Update that created api user                                
        $this->patch(route("tenants.renew-api-user", ["tenant_id" => $tenant->tenant_id, "api_user_id" => $apiUser->api_user_id]), [])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            "status",
            "data" => [
                "api_user_id",
                "api_key",
                "api_secret"
            ],
            "message"
        ]);

        // Delete above records which are created for this test case
        $this->assertEquals(true, $tenant->delete());
        $this->assertEquals(true, $apiUser->delete());        
    }

    /**
     * @test
     * 
     * Renew api user's key, while tenant not found
     * @return void
     */
    public function it_should_return_tenant_not_found_for_updated_secret_key_of_api_user()
    {
        // Generate random tenantId
        $tenantId = rand(99999999,999999999);        

        $apiUser = ApiUser::get()->random();        

        // Update that created api user                                
        $this->patch(route("tenants.renew-api-user", ["tenant_id" => $tenantId, "api_user_id" => $apiUser->api_user_id]), [])
        ->seeStatusCode(404);
    }

    /**
     * @test
     * 
     * Renew api user's key, while api user not found
     * @return void
     */
    public function it_should_return_api_user_not_found_for_updated_secret_key_of_api_user()
    {
        // Get random tenant with api users data
        $tenant = Tenant::with('apiUsers')->get()->random();

        // Generate random apiUserId
        $apiUserId = rand(99999999,999999999);        

        // Update that created api user
        $this->patch(route("tenants.renew-api-user", ["tenant_id" => $tenant->tenant_id, "api_user_id" => $apiUserId]), [])
        ->seeStatusCode(404);
    }

    /**
     * @test
     * 
     * Delete api user
     * @return void
     */
    public function it_should_delete_api_user()
    {
        // Create new tenant
        $tenant = factory(Tenant::class)->create();

        // Create new api user
        $data['api_key'] = str_random(10);
        $data['api_secret'] = str_random(10);

        $apiUser = $tenant->apiUsers()->create($data);        

        // Delete api user for tenant                                
        $this->delete(route("tenants.delete-api-user", ["tenant_id" => $tenant->tenant_id, "api_user_id" => $apiUser->api_user_id]), [])
        ->seeStatusCode(204);
    }

    /**
     * @test
     * 
     * Delete api user, while tenant not found
     * @return void
     */
    public function it_should_return_tenant_not_found_for_delete_api_user()
    {
        // Generate random tenantId
        $tenantId = rand(99999999,999999999);

        // Get random api user
        $apiUser = ApiUser::get()->random();        

        // Delete api user for tenant                                
        $this->delete(route("tenants.delete-api-user", ["tenant_id" => $tenantId, "api_user_id" => $apiUser->api_user_id]), [])
        ->seeStatusCode(404);
    }

    /**
     * @test
     * 
     * Delete api user, while tenant not found
     * @return void
     */
    public function it_should_return_api_user_not_found_for_delete_api_user()
    {
        // Get random tenant
        $tenant =  Tenant::get()->random();

        // Generate random apiUserId
        $apiUserId = rand(99999999,999999999);        

        // Delete api user for tenant                                
        $this->delete(route("tenants.delete-api-user", ["tenant_id" => $tenant->tenant_id, "api_user_id" => $apiUserId]), [])
        ->seeStatusCode(404);
    }
}
