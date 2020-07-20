<?php

namespace Tests\Unit\Http\Controllers\App\Auth;

use App\Helpers\Helpers;
use App\Helpers\LanguageHelper;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\App\Auth\AuthController;
use App\Models\PasswordReset;
use App\Models\TenantOption;
use App\Providers\Passwords\CreatePasswordBroker;
use App\Providers\Passwords\CreatePasswordBrokerManager;
use App\Repositories\TenantOption\TenantOptionRepository;
use App\Repositories\User\UserRepository;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery;
use TestCase;

class UserControllerTest extends TestCase
{
    public function testCreatePassword()
    {
        $request = $this->mock(Request::class);
        $request->shouldReceive('get')
            ->andReturn('test@optimy.com')
            ->shouldReceive('toArray')
            ->andReturn([
                'email' => 'test@optimy.com'
            ])
            ->shouldReceive('all')
            ->shouldReceive('route')
            ->shouldReceive('secure')
            ->shouldReceive('only')
            ->andReturn([
                'email' => 'test@optimy.com'
            ]);

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper->shouldReceive('success');

        $tenantOption = $this->mock(TenantOption::class);
        $tenantOption->shouldReceive('getAttribute');

        $tenantOptionRepository = $this->mock(TenantOptionRepository::class);
        $tenantOptionRepository->shouldReceive('getOptionValueFromOptionName')
            ->andReturn($tenantOption);

        $helpers = $this->mock(Helpers::class);
        $helpers->shouldReceive('getSubDomainFromRequest');

        $languageHelper = $this->mock(LanguageHelper::class);
        $languageHelper->shouldReceive('getLanguageDetails')
            ->andReturn($request);

        $userModel = new User();

        $userRepository = $this->mock(UserRepository::class);
        $userRepository->shouldReceive('findUserByEmail')
            ->andReturn($userModel);

        $passwordReset = $this->mock(PasswordReset::class);
        $passwordReset->shouldReceive('getAttribute');

        $authController = new AuthController(
            $request,
            $responseHelper,
            $tenantOptionRepository,
            $helpers,
            $languageHelper,
            $userRepository,
            $passwordReset
        );

        $passwordBroker = $this->mock(CreatePasswordBroker::class);
        $passwordBroker->shouldReceive('sendCreatePasswordLink');

        $brokerManager = $this->mock(CreatePasswordBrokerManager::class)
            ->shouldAllowMockingProtectedMethods();

        $brokerManager->shouldReceive('resolve')
            ->andReturn($passwordBroker)
            ->shouldReceive('broker')
            ->andReturn($passwordBroker);

        $authController->createPasswordBrokerManager = $brokerManager;

        $this->withoutEvents();

        $response = $authController->createPassword($userModel, $request);

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function testUpdatePassword()
    {
        $request = $this->mock(Request::class);
        $request
            ->shouldReceive('get')
            ->andReturn('token_here')
            ->shouldReceive('toArray')
            ->andReturn([
                'email' => 'test@optimy.com',
                'token' => 'token_here',
                'password' => 'password_here',
                'password_confirmation' => 'password_here'
            ])
            ->shouldReceive('all')
            ->shouldReceive('route')
            ->shouldReceive('remove')
            ->shouldReceive('only')
            ->andReturn([
                'email', 'password', 'password_confirmation', 'token'
            ])
            ->shouldReceive('merge')
            ->shouldReceive('except');

        $responseHelper = $this->mock(ResponseHelper::class);
        $responseHelper->shouldReceive('success');

        $tenantOptionRepository = $this->mock(TenantOptionRepository::class);

        $helpers = $this->mock(Helpers::class);

        $languageHelper = $this->mock(LanguageHelper::class);

        $userModel = new User();

        $userRepository = $this->mock(UserRepository::class);
        $userRepository->shouldReceive('findUserByEmail')
            ->andReturn($userModel);

        $passwordReset = new PasswordReset([
            'token' => '$2y$10$cbCCO.Ndr9kgUcumw2Ln2etgAYbAiDXcbCZypuXPL/lazWCMHJNNS'
        ]);

        $builder = $this->mock(Builder::class);
        $builder->shouldReceive('first')
            ->andReturn($passwordReset);

        $belongsToMany = $this->mock(BelongsToMany::class);
        $belongsToMany->shouldReceive('where')
            ->andReturn($builder);

        $passwordReset = $this->mock(PasswordReset::class);
        $passwordReset
            ->shouldReceive('where')
            ->andReturn($belongsToMany);

        $authController = new AuthController(
            $request,
            $responseHelper,
            $tenantOptionRepository,
            $helpers,
            $languageHelper,
            $userRepository,
            $passwordReset
        );

        $passwordBroker = $this->mock(CreatePasswordBroker::class);
        $passwordBroker->shouldReceive('reset');

        $brokerManager = $this->mock(CreatePasswordBrokerManager::class)
            ->shouldAllowMockingProtectedMethods();

        $brokerManager->shouldReceive('resolve')
            ->andReturn($passwordBroker)
            ->shouldReceive('broker')
            ->andReturn($passwordBroker);

        $authController->createPasswordBrokerManager = $brokerManager;

        $this->withoutEvents();

        $response = $authController->updatePassword($request);

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
