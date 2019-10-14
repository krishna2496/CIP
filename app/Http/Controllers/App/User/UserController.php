<?php
namespace App\Http\Controllers\App\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;
use App\Repositories\UserCustomField\UserCustomFieldRepository;
use App\Repositories\UserFilter\UserFilterRepository;
use App\Repositories\City\CityRepository;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;
use App\User;
use InvalidArgumentException;
use App\Transformations\UserTransformable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\LanguageHelper;
use App\Helpers\Helpers;
use Validator;
use Illuminate\Validation\Rule;
use App\Helpers\S3Helper;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use RestExceptionHandlerTrait, UserTransformable;
    /**
     * @var App\Repositories\User\UserRepository
     */
    private $userRepository;
    
    /**
     * @var App\Repositories\UserCustomField\UserCustomFieldRepository
     */
    private $userCustomFieldRepository;

    /**
     * @var App\Repositories\City\CityRepository
     */
    private $cityRepository;

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
     * @var App\Repositories\UserFilter\UserFilterRepository
     */
    private $userFilterRepository;

    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\User\UserRepository $userRepository
     * @param App\Repositories\UserCustomField\UserCustomFieldRepository $userCustomFieldRepository
     * @param App\Repositories\City\CityRepository $cityRepository
     * @param Illuminate\Http\UserFilterRepository $userFilterRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param App\Helpers\LanguageHelper $languageHelper
     * @param App\Helpers\Helpers $helpers
     * @param App\Helpers\S3Helper $s3helper
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        UserCustomFieldRepository $userCustomFieldRepository,
        CityRepository $cityRepository,
        UserFilterRepository $userFilterRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        Helpers $helpers,
        S3Helper $s3helper
    ) {
        $this->userRepository = $userRepository;
        $this->userCustomFieldRepository = $userCustomFieldRepository;
        $this->cityRepository = $cityRepository;
        $this->userFilterRepository = $userFilterRepository;
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
        $userList = $this->userRepository->listUsers($request->auth->user_id);
        if ($request->has('search')) {
            $userList = $this->userRepository->searchUsers($request->input('search'), $request->auth->user_id);
        }
        $tenantName = $this->helpers->getSubDomainFromRequest($request);
        $users = $userList->map(function (User $user) use ($request, $tenantName) {
            $user = $this->transformUser($user, $tenantName);
            return $user;
        })->all();

        // Set response data
        $apiStatus = Response::HTTP_OK;
        $apiMessage = (empty($users)) ? trans('messages.success.MESSAGE_NO_RECORD_FOUND')
            : trans('messages.success.MESSAGE_USER_LISTING');
        return $this->responseHelper->success(Response::HTTP_OK, $apiMessage, $users);
    }

    /**
     * Get default language of user
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function getUserDefaultLanguage(Request $request): JsonResponse
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
        }
    }

    /**
     * Get user detail.
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $userId = $request->auth->user_id;
        $userDetail = $this->userRepository->findUserDetail($userId);
        $customFields = $this->userCustomFieldRepository->getUserCustomFields($request);
        $userSkillList = $this->userRepository->userSkills($userId);
        $cityList = $this->cityRepository->cityList($userDetail->country_id);
        $tenantLanguages = $this->languageHelper->getTenantLanguageList($request);
        $tenantLanguageCodes = $this->languageHelper->getTenantLanguageCodeList($request);
        $availabilityList = $this->userRepository->getAvailability();

        $languages = $this->languageHelper->getLanguages($request);
        $language = ($request->hasHeader('X-localization')) ?
        $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
        $languageCode = $languages->where('code', $language)->first()->code;
        $userLanguageCode = $languages->where('language_id', $userDetail->language_id)->first()->code;
        $userCustomFieldData = [];
        $userSkillData = [];
        $customFieldsData = $customFields->toArray();
        
        $customFieldsValue = $userDetail->userCustomFieldValue;
        unset($userDetail->userCustomFieldValue);

        if (!empty($customFieldsData) && (isset($customFieldsData))) {
            $returnData = [];
            foreach ($customFieldsData as $key => $value) {
                if ($value) {
                    $arrayKey = array_search($languageCode, array_column($value['translations'], 'lang'));
                    $returnData = $value;
                    unset($returnData['translations']);
                    if (isset($value['translations'][$arrayKey])) {
                        if ($arrayKey !== '') {
                            $returnData['translations']['lang'] = $value['translations'][$arrayKey]['lang'];
                            $returnData['translations']['name'] = $value['translations'][$arrayKey]['name'];
                            if (isset($value['translations'][$arrayKey]['values'])) {
                                $returnData['translations']['values'] = $value['translations'][$arrayKey]['values'];
                            }

                            $userCustomFieldValue = $customFieldsValue->where('field_id', $value['field_id'])
                            ->where('user_id', $userId)->first();
                            $returnData['user_custom_field_value'] = $userCustomFieldValue->value ?? '';
                        }
                    }
                }
                if (!empty($returnData)) {
                    $userCustomFieldData[] = $returnData;
                }
            }
        }

        if (!empty($userSkillList) && (isset($userSkillList))) {
            $returnData = [];
            foreach ($userSkillList as $key => $value) {
                if ($value['skill']) {
                    $arrayKey = array_search($languageCode, array_column($value['skill']['translations'], 'lang'));
                    if ($arrayKey !== '') {
                        $returnData[config('constants.SKILL')][$key]['skill_id'] =
                        $value['skill']['skill_id'];
                        $returnData[config('constants.SKILL')][$key]['skill_name'] =
                        $value['skill']['skill_name'];
                        $returnData[config('constants.SKILL')][$key]['translations'] =
                        $value['skill']['translations'][$arrayKey]['title'];
                    }
                }
            }
            if (!empty($returnData)) {
                $userSkillData = $returnData[config('constants.SKILL')];
            }
        }

        $tenantName = $this->helpers->getSubDomainFromRequest($request);

        $apiData = $userDetail->toArray();
        $apiData['language_code'] = $userLanguageCode;
        $apiData['avatar'] = ((isset($apiData['avatar'])) && $apiData['avatar'] !="") ? $apiData['avatar'] :
        $this->helpers->getUserDefaultProfileImage($tenantName);
        $apiData['custom_fields'] = $userCustomFieldData;
        $apiData['user_skills'] = $userSkillData;
        $apiData['city_list'] = $cityList;
        $apiData['language_list'] = $tenantLanguages;
        $apiData['language_code_list'] = $tenantLanguageCodes;
        $apiData['availability_list'] = $availabilityList;
        
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_USER_FOUND');
        
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
    
    /**
     * Update user data
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $id = $request->auth->user_id;
        // Server side validataions
        $validator = Validator::make(
            $request->all(),
            ["first_name" => "sometimes|required|max:16",
            "last_name" => "sometimes|required|max:16",
            "password" => "sometimes|required|min:8",
            "employee_id" => [
                "sometimes",
                "required",
                "max:16",
                Rule::unique('user')->ignore($id, 'user_id,deleted_at,NULL')],
            "department" => "max:16",
            "manager_name" => "max:16",
            "linked_in_url" => "url|valid_linkedin_url",
            "why_i_volunteer" => "sometimes|required",
            "availability_id" => "integer|exists:availability,availability_id,deleted_at,NULL",
            "timezone_id" => "integer|exists:timezone,timezone_id,deleted_at,NULL",
            "city_id" => "integer|exists:city,city_id,deleted_at,NULL",
            "country_id" => "integer|exists:country,country_id,deleted_at,NULL",
            "custom_fields.*.field_id" => "sometimes|required|exists:user_custom_field,field_id,deleted_at,NULL",
            'skills' => 'present|array',
            'skills.*.skill_id' => 'integer|required|exists:skill,skill_id,deleted_at,NULL']
        );

        // If request parameter have any error
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_USER_INVALID_DATA'),
                $validator->errors()->first()
            );
        }

        // Check language id
        if (isset($request->language_id)) {
            if (!$this->languageHelper->validateLanguageId($request)) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_USER_INVALID_DATA'),
                    trans('messages.custom_error_message.ERROR_USER_INVALID_LANGUAGE')
                );
            }
        }

        // Check if skills reaches maximum limit
        if (!empty($request->skills)) {
            if (count($request->skills) > config('constants.SKILL_LIMIT')) {
                return $this->responseHelper->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                    config('constants.error_codes.ERROR_SKILL_LIMIT'),
                    trans('messages.custom_error_message.SKILL_LIMIT')
                );
            }
        }

        //Remove params
        $request->request->remove("email");

        // Update user filter
        $this->userFilterRepository->saveFilter($request);

        // Update user
        $user = $this->userRepository->update($request->toArray(), $id);

        // Update user custom fields
        if (!empty($request->custom_fields) && isset($request->custom_fields)) {
            $userCustomFields = $this->userRepository->updateCustomFields($request->custom_fields, $id);
        }

        // Update user skills
        $this->userRepository->deleteSkills($id);
        $this->userRepository->linkSkill($request->toArray(), $id);

        // Set response data
        $apiData = ['user_id' => $user->user_id];
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_USER_UPDATED');
        
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
    
    /**
     * Upload profile image of user
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function uploadProfileImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->toArray(), [
            'avatar' => 'required|valid_profile_image'
        ]);

        // If request parameter have any error
        if ($validator->fails()) {
            return $this->responseHelper->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY],
                config('constants.error_codes.ERROR_USER_INVALID_DATA'),
                $validator->errors()->first()
            );
        }

        $userId = $request->auth->user_id;
        $tenantName = $this->helpers->getSubDomainFromRequest($request);
        $avatar = preg_replace('#^data:image/\w+;base64,#i', '', $request->avatar);
        $imagePath = $this->s3helper->uploadProfileImageOnS3Bucket($avatar, $tenantName, $userId);
        
        $userData['avatar'] = $imagePath;
        $this->userRepository->update($userData, $userId);
        
        $apiData = ['avatar' => $imagePath];
        $apiMessage = trans('messages.success.MESSAGE_PROFILE_IMAGE_UPLOADED');
        $apiStatus = Response::HTTP_OK;
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }

    /**
     * store cookie agreement date
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function saveCookieAgreement(Request $request): JsonResponse
    {
        $userId = $request->auth->user_id;
        
        // Update cookie agreement date
        $this->userRepository->updateCookieAgreement($userId);

        // Set response data
        $apiData = ['user_id' => $userId];
        $apiStatus = Response::HTTP_OK;
        $apiMessage = trans('messages.success.MESSAGE_USER_COOKIE_AGREEMENT_ACCEPTED');
        
        return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
    }
}
