<?php
use App\Helpers\Helpers;

class AppTenantOptionTest extends TestCase
{
    /**
     * @test
     *
     * Get all tenant option
     *
     * @return void
     */
    public function it_should_return_all_tenant_options()
    {
        $this->get(route('connect'), [])
          ->seeStatusCode(200)
          ->seeJsonStructure([
            "status",
            "data" =>  [
                "defaultLanguage",
                "defaultLanguageId",
                "language"
            ]
        ]);
    }

    /**
     * @test
     *
     * Get tenant option by option name
     *
     * @return void
     */
    public function it_should_return_tenant_option_by_option_name()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        DB::setDefaultConnection('tenant');
        $tenantOptionName = App\Models\TenantOption::where('option_name', '<>', config('constants.TENANT_OPTION_SLIDER'))->get()->random()->option_name;
        DB::setDefaultConnection('mysql');

        $params = [
            'option_name' => $tenantOptionName
        ];

        $token = Helpers::getTestUserToken($user->user_id);
        $this->post("app/tenant-option", $params, ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        $user->delete();
    }

    /**
     * @test
     *
     * Return error if no tenant option found
     *
     * @return void
     */
    public function it_should_return_error_for_invalid_tenant_option_name()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        $optionName = str_random(20);
        $params = [
            'option_name' => $optionName
        ];

        $token = Helpers::getTestUserToken($user->user_id);
        $this->post("app/tenant-option", $params, ['token' => $token])
        ->seeStatusCode(200)
        ->seeJsonStructure([
            'status',
            'message',
        ]);
        $user->delete();
    }
}
