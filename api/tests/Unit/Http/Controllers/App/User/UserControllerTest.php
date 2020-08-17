<?php

namespace Tests\Unit\Http\Controllers\App\Auth;

use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\S3Helper;
use App\Http\Controllers\App\User\UserController;
use App\Models\TenantOption;
use App\Repositories\City\CityRepository;
use App\Repositories\TenantOption\TenantOptionRepository;
use App\Repositories\UserCustomField\UserCustomFieldRepository;
use App\Repositories\UserFilter\UserFilterRepository;
use App\Repositories\User\UserRepository;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use TestCase;

class UserControllerTest extends TestCase
{
    public function testInviteUser()
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('toArray')
            ->andReturn([
                'email' => 'test@optimy.com'
            ])
            ->shouldReceive('get')
            ->andReturn('test@optimy.com')
            ->shouldReceive('all')
            ->shouldReceive('route')
            ->shouldReceive('secure');

        $userModel = new User();

        $userModel = $this->mock(User::class);
        $userModel->shouldReceive('getAttribute')
            ->shouldReceive('notify')
            ->shouldReceive('setAttribute')
            ->shouldReceive('save');

        $userRepository = $this->mock(UserRepository::class);
        $userRepository->shouldReceive('findUserByEmail')
            ->andReturn($userModel);

        $userCustomFieldRepository = $this->mock(UserCustomFieldRepository::class);
        $cityRepository = $this->mock(CityRepository::class);
        $userFilterRepository = $this->mock(UserFilterRepository::class);
        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper->shouldReceive('success');

        $languageHelper = $this->mock(LanguageHelper::class);
        $languageHelper->shouldReceive('getLanguageDetails')
            ->andReturn($request);

        $helpers = $this->mock(Helpers::class);
        $helpers->shouldReceive('getSubDomainFromRequest');

        $s3Helper = $this->mock(S3Helper::class);

        $tenantOption = $this->mock(TenantOption::class);
        $tenantOption->shouldReceive('getAttribute');

        $tenantOptionRepository = $this->mock(TenantOptionRepository::class);
        $tenantOptionRepository->shouldReceive('getOptionValueFromOptionName')
            ->andReturn($tenantOption)
            ->shouldReceive('getOptionValue');

        $authController = new UserController(
            $userRepository,
            $userCustomFieldRepository,
            $cityRepository,
            $userFilterRepository,
            $responseHelper,
            $languageHelper,
            $helpers,
            $s3Helper,
            $tenantOptionRepository
        );

        $this->withoutEvents();

        $response = $authController->inviteUser($userModel, $request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    /**
     * Mock an object
     *
     * @param string name
     *
     * @return Mockery
     */
    private function mock($class)
    {
        return Mockery::mock($class);
    }
}
