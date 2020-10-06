<?php

namespace Tests\Unit\Http\Controllers\App\Auth;

use Mockery;
use TestCase;
use Validator;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\PasswordReset;
use App\Helpers\ResponseHelper;
use App\Helpers\LanguageHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Events\User\UserActivityLogEvent;
use App\Repositories\User\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Controllers\App\Auth\AuthController;
use App\Repositories\TenantOption\TenantOptionRepository;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class AuthControllerTest extends TestCase
{
	private function getController(
		Request $request,
		ResponseHelper $responseHelper = null,
		TenantOptionRepository $tenantOptionRepository = null,
		Helpers $helpers = null,
		LanguageHelper $languageHelper = null,
		UserRepository $userRepository = null,
		PasswordReset $passwordReset = null
	) {
		$responseHelper = $responseHelper ?? $this->mock(ResponseHelper::class);
		$tenantOptionRepository = $tenantOptionRepository ?? $this->mock(TenantOptionRepository::class);
		$helpers = $helpers ?? $this->mock(Helpers::class);
		$languageHelper = $languageHelper ?? $this->mock(LanguageHelper::class);
		$userRepository = $userRepository ?? $this->mock(UserRepository::class);
		$passwordReset = $passwordReset ?? $this->mock(PasswordReset::class);

		return new AuthController(
			$request,
			$responseHelper,
			$tenantOptionRepository,
			$helpers,
			$languageHelper,
			$userRepository,
			$passwordReset
		);
	}

	/**
	* @testdox Test change password
	*
	* @return void
	*/
	public function testChangePasswordSuccess()
	{
		$this->expectsEvents(UserActivityLogEvent::class);
		$symfonyRequest = $this->mock(SymfonyRequest::class);
		$symfonyRequest->password = Hash::make('old-password');
		$symfonyRequest->user_id = 1;
		$symfonyRequest->email = 'testuser@email.com';

		$request = $this->mock(Request::class);
		$request
			->shouldReceive('header')
			->shouldReceive('toArray')
			->andReturn([
				'old_password' => 'old-password',
				'password' => 'Passw0rd',
				'confirm_password' => 'Passw0rd'
			]);
		$request->auth = $symfonyRequest;
		$request->old_password = 'old-password';
		$request->password = 'Passw0rd';

		$validator = $this->mock(\Illuminate\Validation\Validator::class);
		$validator
			->shouldReceive('fails')
			->andReturn(false);

		$responseHelper = $this->mock(ResponseHelper::class);
		$responseHelper
			->shouldReceive('success')
			->once()
			->with(
				Response::HTTP_OK,
				trans('messages.success.MESSAGE_PASSWORD_CHANGE_SUCCESS'),
				[]
			)
			->andReturn(new JsonResponse(
				[],
				Response::HTTP_OK
			));

		$helpers = $this->mock(Helpers::class);
		$helpers
			->shouldReceive('getSubDomainFromRequest')
			->once()
			->with($request)
			->andReturn('tenant-name');

		$helpers
			->shouldReceive('getJwtToken')
			->once()
			->with(1, 'tenant-name')
			->andReturn('tenant-token');

		$userRepository = $this->mock(UserRepository::class);
		$userRepository
			->shouldReceive('changePassword')
			->once()
			->andReturn(true);

		$controller = $this->getController(
			$request,
			$responseHelper,
			null,
			$helpers,
			null,
			$userRepository,
			null
		);

		$response = $controller->changePassword($request);
		$this->assertInstanceOf(JsonResponse::class, $response);
		$this->assertEquals(
			[],
			json_decode($response->getContent(), true)
		);
	}

	/**
	* @testdox Test change password 
	*
	* @return void
	*/
	public function testChangePasswordInvalidPassword()
	{
		$symfonyRequest = $this->mock(SymfonyRequest::class);
		$symfonyRequest->password = Hash::make('old-password');
		$symfonyRequest->user_id = 1;
		$symfonyRequest->email = 'testuser@email.com';

		$request = $this->mock(Request::class);
		$request
			->shouldReceive('header')
			->shouldReceive('toArray')
			->andReturn([
				'old_password' => 'old-password',
				'password' => 'password',
				'confirm_password' => 'password'
			]);
		$request->auth = $symfonyRequest;
		$request->old_password = 'old-password';
		$request->password = 'password';

		$errors = new Collection([
			trans('messages.custom_error_message.ERROR_PASSWORD_VALIDATION_MESSAGE')
		]);

		$validator = $this->mock(\Illuminate\Validation\Validator::class);
		$validator
			->shouldReceive('fails')
			->andReturn(true)
			->shouldReceive('errors')
			->andReturn($errors);

		$responseHelper = $this->mock(ResponseHelper::class);
		$responseHelper
			->shouldReceive('error')
			->once()
			->with(
				Response::HTTP_UNPROCESSABLE_ENTITY,
				Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
				config('constants.error_codes.ERROR_INVALID_DETAIL'),
				trans('messages.custom_error_message.ERROR_PASSWORD_VALIDATION_MESSAGE')
			)
			->andReturn(new JsonResponse(
				[
					'errors' => [
						'message' => trans('messages.custom_error_message.ERROR_PASSWORD_VALIDATION_MESSAGE')
					]
				],
				Response::HTTP_UNPROCESSABLE_ENTITY
			));

		$helpers = $this->mock(Helpers::class);
		$helpers
			->shouldReceive('getSubDomainFromRequest')
			->never()
			->with($request)
			->andReturn('tenant-name');

		$helpers
			->shouldReceive('getJwtToken')
			->never()
			->with(1, 'tenant-name')
			->andReturn('tenant-token');

		$userRepository = $this->mock(UserRepository::class);
		$userRepository
			->shouldReceive('changePassword')
			->never()
			->andReturn(true);

		$controller = $this->getController(
			$request,
			$responseHelper,
			null,
			$helpers,
			null,
			$userRepository,
			null
		);

		$response = $controller->changePassword($request);
		$this->assertInstanceOf(JsonResponse::class, $response);
		$this->assertEquals(
			[
				'errors' => [
					'message' => trans('messages.custom_error_message.ERROR_PASSWORD_VALIDATION_MESSAGE')
				]
			],
			json_decode($response->getContent(), true)
		);
	}

	/**
	* @testdox Test change password
	*
	* @return void
	*/
	public function testChangePasswordInvalidOldPassword()
	{
		$symfonyRequest = $this->mock(SymfonyRequest::class);
		$symfonyRequest->password = Hash::make('HELLO-PASSWORD');
		$symfonyRequest->user_id = 1;
		$symfonyRequest->email = 'testuser@email.com';

		$request = $this->mock(Request::class);
		$request
			->shouldReceive('header')
			->shouldReceive('toArray')
			->andReturn([
				'old_password' => 'old-password',
				'password' => 'Passw0rd',
				'confirm_password' => 'Passw0rd'
			]);
		$request->auth = $symfonyRequest;
		$request->old_password = 'old-password';
		$request->password = 'Passw0rd';

		$validator = $this->mock(\Illuminate\Validation\Validator::class);
		$validator
			->shouldReceive('fails')
			->andReturn(false);

		$responseHelper = $this->mock(ResponseHelper::class);
		$responseHelper
			->shouldReceive('error')
			->once()
			->with(
				Response::HTTP_UNPROCESSABLE_ENTITY,
				Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
				config('constants.error_codes.ERROR_INVALID_DETAIL'),
				trans('messages.custom_error_message.ERROR_OLD_PASSWORD_NOT_MATCHED')
			)
			->andReturn(new JsonResponse(
				[
					'errors' => [
					'message' => trans('messages.custom_error_message.ERROR_OLD_PASSWORD_NOT_MATCHED')
					]
				],
				Response::HTTP_UNPROCESSABLE_ENTITY
			));

		$helpers = $this->mock(Helpers::class);
		$helpers
			->shouldReceive('getSubDomainFromRequest')
			->never()
			->with($request)
			->andReturn('tenant-name');

		$helpers
			->shouldReceive('getJwtToken')
			->never()
			->with(1, 'tenant-name')
			->andReturn('tenant-token');

		$userRepository = $this->mock(UserRepository::class);
		$userRepository
			->shouldReceive('changePassword')
			->never()
			->andReturn(true);

		$controller = $this->getController(
			$request,
			$responseHelper,
			null,
			$helpers,
			null,
			$userRepository,
			null
		);

		$response = $controller->changePassword($request);
		$this->assertInstanceOf(JsonResponse::class, $response);
		$this->assertEquals(
			[
				'errors' => [
					'message' => trans('messages.custom_error_message.ERROR_OLD_PASSWORD_NOT_MATCHED')
				]
			],
			json_decode($response->getContent(), true)
		);
	}

	/**
	* Mock an object
	*
	* @param string name
	* @return Mockery
	*/
	private function mock($class)
	{
		return Mockery::mock($class);
	}
}
