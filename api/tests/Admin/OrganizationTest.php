<?php
use App\Helpers\Helpers;
use App\Models\Organization;

class OrganizationTest extends TestCase
{
    /**
     * @test
     *
     * Create organizations
     *
     * @return void
     */
    public function it_should_create_organization()
    {
        $params = [
            "name"=> "testOrganization",
            "legal_number"=>'9874563201',
            "phone_number"=>'9874563201',
            "address_line_1"=>'address 1',
            "address_line_2"=>'address_2',
            "city_id"=>1,
            "state_id"=>1,
            "country_id"=>1,
            "postal_code"=>"2458"
        ];

        DB::setDefaultConnection('mysql');
        $response = $this->post('organizations', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(201);

        $organizationsId = json_decode($response->response->getContent())->data->organization_id;
        
        Organization::whereNull('deleted_at')->where('organization_id', $organizationsId)->delete();
    }

    /**
     * @test
     *
     * Create organization : Test server side validation
     *
     * @return void
     */
    public function it_should_check_server_side_validation_for_create_organization()
    {
        $params = [
            "name"=> "testOrganization",
            "legal_number"=>'9874563201',
            "phone_number"=>'98745632',
            "address_line_1"=>'address 1',
            "address_line_2"=>'address_2',
            "city_id"=>1,
            "state_id"=>1,
            "country_id"=>1,
            "postal_code"=>"2458"
        ];

        DB::setDefaultConnection('mysql');
        $response = $this->post('organizations', $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);
    }

    /**
     * @test
     *
     * Update organization
     *
     * @return void
     */
    public function it_should_update_organization()
    {
        $connection = 'tenant';
        $organization = factory(\App\Models\Organization::class)->make();
        $organization->setConnection($connection);
        $organization->save();
        $organizationId = $organization->organization_id;
        
        $params = [
            "name"=> "testOrganizationupdated",
            "legal_number"=>'9874563201',
            "phone_number"=>'9874563201',
            "city_id"=>1,
            "state_id"=>1,
            "country_id"=>1,
            "postal_code"=>"2458"
        ];

        DB::setDefaultConnection('mysql');
        $response = $this->patch('organizations/'.$organizationId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        Organization::whereNull('deleted_at')->where('organization_id', $organizationId)->delete();
    }

    /**
     * @test
     *
     * Update organization : server side validation
     *
     * @return void
     */
    public function it_should_throw_server_side_validation_update_organization()
    {
        $connection = 'tenant';
        $organization = factory(\App\Models\Organization::class)->make();
        $organization->setConnection($connection);
        $organization->save();
        $organizationId = $organization->organization_id;
        
        $params = [
            "name"=> "testOrganizationupdated",
            "legal_number"=>'123123',
            "phone_number"=>'adawdawd',
            "city_id"=>1,
            "state_id"=>1,
            "country_id"=>1,
            "postal_code"=>"2458"
        ];

        DB::setDefaultConnection('mysql');
        $response = $this->patch('organizations/'.$organizationId, $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(422);

        Organization::whereNull('deleted_at')->where('organization_id', $organizationId)->delete();
    }

    /**
     * @test
     *
     * Update organization : Model not found
     *
     * @return void
     */
    public function it_should_throw_model_not_found_when_update_organization()
    {
        $params = [
            "name"=> "testOrganizationupdated",
            "legal_number"=>'9874563201',
            "phone_number"=>'9874563201',
            "city_id"=>1,
            "state_id"=>1,
            "country_id"=>1,
            "postal_code"=>"2458"
        ];
        DB::setDefaultConnection('mysql');
        $response = $this->patch('organizations/'.rand(), $params, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * Delete organization
     *
     * @return void
     */
    public function it_should_delete_organization()
    {
        $connection = 'tenant';
        $organization = factory(\App\Models\Organization::class)->make();
        $organization->setConnection($connection);
        $organization->save();
        $organizationId = $organization->organization_id;

        DB::setDefaultConnection('mysql');
        $response = $this->delete('organizations/'.$organizationId, [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(204);
    }

    /**
     * @test
     *
     * Delete organization : Model not found
     *
     * @return void
     */
    public function it_should_through_model_not_found_delete_organization()
    {
        DB::setDefaultConnection('mysql');
        $response = $this->delete('organizations/'.rand(), [], ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * View organization
     *
     * @return void
     */
    public function it_should_show_organization_details()
    {
        $connection = 'tenant';
        $organization = factory(\App\Models\Organization::class)->make();
        $organization->setConnection($connection);
        $organization->save();
        $organizationId = $organization->organization_id;
        
        DB::setDefaultConnection('mysql');
        $response = $this->get('organizations/'.$organizationId, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        Organization::whereNull('deleted_at')->where('organization_id', $organizationId)->delete();
    }

    /**
     * @test
     *
     * View organization : Model not found
     *
     * @return void
     */
    public function it_should_throuh_model_not_found_organization_details()
    {
        DB::setDefaultConnection('mysql');
        $response = $this->get('organizations/'.rand(), ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * Fetch organizations
     *
     * @return void
     */
    public function it_should_fetch_all_organizations()
    {
        $organizationIdsArray = [];
        $connection = 'tenant';
                
        for ($i=0; $i<5; $i++) {
            $organization = factory(\App\Models\Organization::class)->make();
            $organization->setConnection($connection);
            $organization->save();
            $organizationId = $organization->organization_id;
            $organizationName = $organization->name;
            array_push($organizationIdsArray, $organizationId);
        }

        $response = $this->get('organizations', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        Organization::whereIn('organization_id', $organizationIdsArray)->delete();
    }

    /**
     * @test
     *
     * Fetch organizations with search parameter
     *
     * @return void
     */
    public function it_should_fetch_all_organizations_with_search()
    {
        $organizationIdsArray = [];
        $connection = 'tenant';
                
        for ($i=0; $i<5; $i++) {
            $organization = factory(\App\Models\Organization::class)->make();
            $organization->setConnection($connection);
            $organization->save();
            $organizationId = $organization->organization_id;
            $organizationName = $organization->name;
            array_push($organizationIdsArray, $organizationId);
        }

        $response = $this->get('organizations?order=asc&search='.$organizationName, ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(200);

        Organization::whereIn('organization_id', $organizationIdsArray)->delete();
    }

    /**
     * @test
     *
     * Fetch organizations through invalid argument
     *
     * @return void
     */
    public function it_should_through_invalid_argument_while_fetch_all_organizations()
    {
        $organizationIdsArray = [];
        $connection = 'tenant';
                
        for ($i=0; $i<5; $i++) {
            $organization = factory(\App\Models\Organization::class)->make();
            $organization->setConnection($connection);
            $organization->save();
            $organizationId = $organization->organization_id;
            $organizationName = $organization->name;
            array_push($organizationIdsArray, $organizationId);
        }

        $response = $this->get('organizations?order=test', ['Authorization' => 'Basic '.base64_encode(env('API_KEY').':'.env('API_SECRET'))])
        ->seeStatusCode(400);
        
        Organization::whereIn('organization_id', $organizationIdsArray)->delete();
    }
}
