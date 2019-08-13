<?php
namespace App\Http\Controllers\App\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;
use App\Repositories\UserCustomField\UserCustomFieldRepository;
use App\Repositories\Skill\SkillRepository;
use App\Repositories\Country\CountryRepository;
use App\Repositories\City\CityRepository;
use App\Repositories\Timezone\TimezoneRepository;
use App\Helpers\ResponseHelper;
use App\Traits\RestExceptionHandlerTrait;
use App\User;
use InvalidArgumentException;
use App\Transformations\UserTransformable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\LanguageHelper;
use App\Helpers\Helpers;

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
     * @var App\Repositories\Skill\SkillRepository
     */
    private $skillRepository;

    /**
     * @var App\Repositories\Country\CountryRepository
     */
    private $countryRepository;

    /**
     * @var App\Repositories\City\CityRepository
     */
    private $cityRepository;

    /**
     * @var App\Repositories\Timezone\TimezoneRepository
     */
    private $timeZoneRepository;
    
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
     * Create a new controller instance.
     *
     * @param App\Repositories\User\UserRepository $userRepository
     * @param App\Repositories\UserCustomField\UserCustomFieldRepository $userCustomFieldRepository
     * @param App\Repositories\Skill\SkillRepository $skillRepository
     * @param App\Repositories\Country\CountryRepository $countryRepository
     * @param App\Repositories\City\CityRepository $cityRepository
     * @param App\Repositories\Timezone\TimezoneRepository $timeZoneRepository
     * @param Illuminate\Http\ResponseHelper $responseHelper
     * @param App\Helpers\LanguageHelper $languageHelper
     * @param App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(
        UserRepository $userRepository,
        UserCustomFieldRepository $userCustomFieldRepository,
        SkillRepository $skillRepository,
        CountryRepository $countryRepository,
        CityRepository $cityRepository,
        TimezoneRepository $timeZoneRepository,
        ResponseHelper $responseHelper,
        LanguageHelper $languageHelper,
        Helpers $helpers
    ) {
        $this->userRepository = $userRepository;
        $this->userCustomFieldRepository = $userCustomFieldRepository;
        $this->skillRepository = $skillRepository;
        $this->countryRepository = $countryRepository;
        $this->cityRepository = $cityRepository;
        $this->timeZoneRepository = $timeZoneRepository;
        $this->responseHelper = $responseHelper;
        $this->languageHelper = $languageHelper;
        $this->helpers = $helpers;
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
     * Get user detail.
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $userId = $request->auth->user_id;
            $userDetail = $this->userRepository->findUserDetail($userId);
            $customFields = $this->userCustomFieldRepository->getUserCustomFields($request);
            $userSkillList = $this->userRepository->userSkills($userId);
            $skillList = $this->skillRepository->skillList($request);
            $countryList = $this->countryRepository->countryList();
            $cityList = $this->cityRepository->cityList($userDetail->country_id);
            $timezoneList = $this->timeZoneRepository->getTimezoneList();
            $tenantLanguages = $this->languageHelper->getTenantLanguageList($request);
            $availabilityList = $this->userRepository->getAvailability();

            $languages = $this->languageHelper->getLanguages($request);
            $language = ($request->hasHeader('X-localization')) ?
            $request->header('X-localization') : env('TENANT_DEFAULT_LANGUAGE_CODE');
            $languageCode = $languages->where('code', $language)->first()->code;
            $userCustomFieldData = [];
            $userSkillData = [];
            $allSkillData = [];
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
                        if ($arrayKey !== '') {
                            $returnData['translations']['lang'] = $value['translations'][$arrayKey]['lang'];
                            $returnData['translations']['name'] = $value['translations'][$arrayKey]['name'];
                            $returnData['translations']['values'] = $value['translations'][$arrayKey]['values'];
                            $returnData['user_custom_field_value'] = $customFieldsValue->where(
                                'field_id',
                                $value['field_id']
                            )->first()->value;
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

            if (!empty($skillList) && (isset($skillList))) {
                $returnData = [];
                foreach ($skillList as $key => $value) {
                    if ($value) {
                        $arrayKey = array_search($languageCode, array_column($value['translations'], 'lang'));
                        if ($arrayKey !== '') {
                            $returnData[$value['skill_id']] = $value['translations'][$arrayKey]['title'];
                        }
                    }
                }
                if (!empty($returnData)) {
                    $allSkillData = $returnData;
                }
            }

            $apiData = $userDetail->toArray();
            $apiData['language_code'] = $languageCode;
            $apiData['custom_fields'] = $userCustomFieldData;
            $apiData['user_skills'] = $userSkillData;
            $apiData['skill_list'] = $allSkillData;
            $apiData['country_list'] = $countryList;
            $apiData['city_list'] = $cityList;
            $apiData['timezone_list'] = $timezoneList;
            $apiData['language_list'] = $tenantLanguages;
            $apiData['availability_list'] = $availabilityList;
            if (isset($userDetail->avatar)) {
                $type = pathinfo($userDetail->avatar, PATHINFO_EXTENSION);
                $imageData = file_get_contents($userDetail->avatar);
                $avatarBase64 = 'data:image/' . $type . ';base64,' . base64_encode($imageData);
                $apiData['avatar_base64'] = $avatarBase64;
            }
           
            $apiStatus = Response::HTTP_OK;
            $apiMessage = trans('messages.success.MESSAGE_USER_FOUND');
            
            return $this->responseHelper->success($apiStatus, $apiMessage, $apiData);
        } catch (InvalidArgumentException $e) {
            return $this->invalidArgument(
                config('constants.error_codes.ERROR_INVALID_ARGUMENT'),
                trans('messages.custom_error_message.ERROR_INVALID_ARGUMENT')
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
}
