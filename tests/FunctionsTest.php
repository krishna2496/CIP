<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use App\Helpers\EmailHelper;
use Illuminate\Support\Str;
use App\Models\Tenant;
use App\Jobs\CompileScssFiles;

class FunctionsTest extends TestCase
{
    /**
     * It will send test email
     * 
     * @test
     * @return bool
     */
    public function it_should_send_testing_email()
    {

        $emailHelper = new EmailHelper();

        $params['to'] = config('constants.ADMIN_EMAIL_ADDRESS'); //required
        $params['template'] = config('constants.EMAIL_TEMPLATE_FOLDER').'.'.config('constants.EMAIL_TESTING_TEMPLATE'); 
        $params['subject'] = "PHP Unit Test : Testing Mail";

        $this->assertTrue($emailHelper->sendEmail($params));
    }

    /**
     * It should throw internal server error
     * 
     * @test
     * @return bool
     */
    public function it_should_return_internal_server_error_exception()
    {
        $this->get("", [])
        ->seeStatusCode(500);
    }
}
