<?php
namespace App\Http\Controllers\App\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;
use App\Traits\RestExceptionHandlerTrait;
use App\Helpers\ResponseHelper;
use App\User;
use InvalidArgumentException;
use App\Transformations\UserTransformable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\LanguageHelper;
use App\Helpers\Helpers;
use App\Helpers\S3Helper;
use Validator;
use Illuminate\Support\Facades\Storage;

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
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * @var App\Helpers\S3Helper
     */
    private $s3helper;
    

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\User\UserRepository $userRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param App\Helpers\LanguageHelper $languageHelper
     * @param App\Helpers\Helpers $helpers
     * @param App\Helpers\S3Helper $s3helper
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        Helpers $helpers,
        S3Helper $s3helper
    ) {
        $this->userRepository = $userRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
        $this->s3helper = $s3helper;
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

            $users = $userList->map(function (User $user) use ($request) {
                $user = $this->transformUser($user);
                $user->avatar = isset($user->avatar) ? $user->avatar :
                $this->helpers->getDefaultProfileImage($request);
                return $user;
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
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        } catch (\PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Add skill to user
     *
     * @param  \Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function linkSkill(Request $request): JsonResponse
    {
        try {
            $id = $request->auth->user_id;
            $validator = Validator::make($request->toArray(), [
                'skills' => 'required',
                'skills.*.skill_id' => 'required|exists:skill,skill_id,deleted_at,NULL',
            ]);

            // If request parameter have any error
            if ($validator->fails()) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_SKILL_INVALID_DATA'),
                    $validator->errors()->first()
                );
            }

            // Check if skills reaches maximum limit
            if (count($request->skills) > config('constants.SKILL_LIMIT')) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_SKILL_LIMIT'),
                    trans('messages.custom_error_message.SKILL_LIMIT')
                );
            }

            //Delete user skills
            $this->userRepository->deleteSkills($id);

            $this->userRepository->linkSkill($request->toArray(), $id);

            // Set response data
            $apiStatus = Response::HTTP_CREATED;
            $apiMessage = trans('messages.success.MESSAGE_USER_SKILLS_CREATED');
            return $this->responseHelper->success($apiStatus, $apiMessage);
        } catch (PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_DATABASE_OPERATIONAL')
            );
        } catch (ModelNotFoundException $e) {
            return $this->modelNotFound(
                config('constants.error_codes.ERROR_USER_NOT_FOUND'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }

    /**
     * Upload profile photo of user
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function uploadProfileImage(Request $request)
    {
        try {
            $userId = $request->auth->user_id;
            $tenantName = $this->helpers->getSubDomainFromRequest($request);
            $imagePath = $this->s3helper->uploadProfileImageOnS3Bucket($request->avatar, $tenantName, $userId);
            
            $userData['avatar'] = $imagePath;
            $this->userRepository->update($userData, $userId);
            
            $apiMessage = trans('messages.success.MESSAGE_PROFILE_IMAGE_UPLOADED');
            $apiStatus = Response::HTTP_OK;
            return $this->responseHelper->success(Response::HTTP_OK, $apiMessage);
        } catch (S3Exception $e) {
            return $this->s3Exception(
                config('constants.error_codes.ERROR_FAILED_TO_RESET_STYLING'),
                trans('messages.custom_error_message.ERROR_FAILED_TO_RESET_STYLING')
            );
        } catch (\PDOException $e) {
            return $this->PDO(
                config('constants.error_codes.ERROR_DATABASE_OPERATIONAL'),
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        } catch (\Exception $e) {
            return $this->badRequest(trans('messages.custom_error_message.ERROR_OCCURRED'));
        }
    }
}
