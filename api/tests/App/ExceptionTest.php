<?php
use App\Helpers\Helpers;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\BucketNotFoundException;
use App\Exceptions\TenantDomainNotFoundException;

class ExceptionTest extends TestCase
{
    /**
     * @test
     *
     * It should throw exception MethodNotAllowedHttpException
     *
     * @return void
     */
    public function it_should_return_method_not_allow_http_exception()
    {
        $connection = 'tenant';
        $user = factory(\App\User::class)->make();
        $user->setConnection($connection);
        $user->save();

        DB::setDefaultConnection('tenant');
        $countryId = App\Models\Country::get()->random()->country_id;

        $token = Helpers::getJwtToken($user->user_id, env('DEFAULT_TENANT'));
        $this->post('/app/city/'.$countryId, ['token' => $token])
        ->seeStatusCode(405);
        
        $user->delete();
    }

    /**
     * @test
     *
     * It should throw exception FileDownloadException
     *
     * @return void
     */
    public function it_should_return_bucket_not_found_exception()
    {
        $this->expectException(BucketNotFoundException::class);
        $responseHelper = new \App\Helpers\ResponseHelper();
        $s3Helpers = new \App\Helpers\S3Helper($responseHelper);
        $s3Helpers->getAllScssFiles(str_random('5'));
    }

    /**
     * @test
     *
     * It should throw exception TenantDomainNotFoundException
     *
     * @return void
     */
    public function it_should_return_tenant_domain_not_found_exception()
    {        
        $fqdn = str_random('5');
        $missionId = rand(1,100);
        $langId = rand(1,5);
        $this->get('/social-sharing/'.$fqdn.'/'.$missionId.'/'.$langId)
        ->seeStatusCode(404);
    }

    /**
     * @test
     *
     * It should throw internal server error
     *
     * @return void
     */
    public function it_should_return_internal_server_error_exception()
    {
        $token = str_random(30);
        $randomUrl = str_random(5);
        DB::setDefaultConnection('mysql');
        $this->get("/app/$randomUrl", ['token' => $token])
        ->seeStatusCode(500);
    }
    
    
}
