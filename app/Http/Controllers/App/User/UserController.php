<?php
namespace App\Http\Controllers\App\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;
use Validator;
use App\User;
use InvalidArgumentException;
use App\Transformations\UserTransformable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\LanguageHelper;

class UserController extends Controller
{
    use RestExceptionHandlerTrait, UserTransformable;
    /**
     * @var App\Repositories\User\UserRepository
     */
    private $userRepository;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;
    
    /**
     * @var App\Helpers\LanguageHelper
     */
    private $languageHelper;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\User\UserRepository $userRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param App\Helpers\LanguageHelper
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper
    )
    {
        $this->userRepository = $userRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userList = $this->userRepository->listUsers($request->auth->user_id);
            if ($request->has('search')) {
                $userList = $this->userRepository->searchUsers($request->input('search'), $request->auth->user_id);
            }

            $users = $userList->map(function (User $user) {
                return $this->transformUser($user);
            })->all();

            // Set response data
            $apiStatus = Response::HTTP_OK;
            $apiMessage = (empty($users)) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND')
             : trans('messages.success.MESSAGE_USER_LISTING');
            return $this->responseHelper->success(Response::HTTP_OK, $apiMessage, $users);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Get default language of user
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function getUserDefaultLanguage(Request $request)
    {
        try {
            $email = $request->get('email');
            $user = $this->userRepository->getUserByEmail($email);

            $userLanguage['default_language_id'] = $user->language_id;

            $apiStatus = Response::HTTP_OK;
            return $this->responseHelper->success(Response::HTTP_OK, '', $userLanguage);
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                $e->getMessage()
            );
        } catch (\PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                $e->getMessage()
            );
        } catch (\Exception $e) {
            
            return $this->badRequest($e->getMessage());
        }
    }
}
