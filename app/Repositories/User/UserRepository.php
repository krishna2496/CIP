<?php
namespace App\Repositories\User;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use App\Repositories\User\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use App\User;
use App\Helpers\Helpers;
use App\Helpers\ResponseHelper;
use App\Models\UserSkill;
use App\Models\UserCustomFieldValue;
use App\Models\Availability;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository implements UserInterface
{
    /**
     * @var App\User
     */
    public $user;

    /**
     * @var App\Models\UserSkill
     */
    public $userSkill;

    /**
     * @var App\Models\UserCustomFieldValue
     */
    public $userCustomFieldValue;

    /**
     * @var App\Models\Availability
     */
    public $availability;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new User repository instance.
     *
     * @param  App\User $user
     * @param  App\Models\UserSkill $userSkill
     * @param  App\Models\UserCustomFieldValue $userCustomFieldValue
     * @param  App\Models\Availability $availability
     * @param  App\Helpers\ResponseHelper $responseHelper
     * @param  App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(
        User $user,
        UserSkill $userSkill,
        UserCustomFieldValue $userCustomFieldValue,
        Availability $availability,
        ResponseHelper $responseHelper,
        Helpers $helpers
    ) {
        $this->user = $user;
        $this->userSkill = $userSkill;
        $this->userCustomFieldValue = $userCustomFieldValue;
        $this->availability = $availability;
        $this->responseHelper = $responseHelper;
        $this->helpers = $helpers;
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param array $request
     * @return App\User
     */
    public function store(array $request): User
    {
        return $this->user->create($request);
    }
    
    /**
     * Get listing of users
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function userList(Request $request): LengthAwarePaginator
    {
        $tenantName = $this->helpers->getSubDomainFromRequest($request);
        $defaultAvatarImage = $this->helpers->getUserDefaultProfileImage($tenantName);

        $userQuery = $this->user->selectRaw("first_name, last_name, email, password, 
        case when(avatar = '' || avatar is null) then '$defaultAvatarImage' else avatar end as avatar, 
        timezone_id, availability_id, why_i_volunteer, employee_id, department,
         manager_name, city_id, country_id, profile_text, linked_in_url, status, language_id, title")
        ->with('city', 'country', 'timezone');
        
        if ($request->has('search')) {
            $userQuery->where(function ($query) use ($request) {
                $query->orWhere('first_name', 'like', '%' . $request->input('search') . '%');
                $query->orWhere('last_name', 'like', '%' . $request->input('search') . '%');
            });
        }
        if ($request->has('order')) {
            $orderDirection = $request->input('order', 'asc');
            $userQuery->orderBy('user_id', $orderDirection);
        }
        
        return $userQuery->paginate($request->perPage);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  array  $request
     * @param  int  $id
     * @return App\User
     */
    public function update(array $request, int $id): User
    {
        $user = $this->user->findOrFail($id);
        $user->update($request);
        return $user;
    }
    
    /**
     * Find specified resource in storage.
     *
     * @param  int  $id
     * @return App\User
     */
    public function find(int $id): User
    {
        return $this->user->findUser($id);
    }

    /**
     * Remove specified resource in storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->user->deleteUser($id);
    }
    
    /**
     * Store a newly created resource into database
     *
     * @param array $request
     * @param int $id
     * @return bool
     */
    public function linkSkill(array $request, int $id): bool
    {
        $this->user->findOrFail($id);
        if (!empty($request['skills'])) {
            foreach ($request['skills'] as $value) {
                $this->userSkill->linkUserSkill($id, $value['skill_id']);
            }
        }
        return true;
    }
    
    /**
     * Remove the specified resource from storage
     *
     * @param array $request
     * @param int $id
     * @return bool
     */
    public function unlinkSkill(array $request, int $id): bool
    {
        $this->user->findOrFail($id);
        $userSkill = $this->userSkill;
        foreach ($request['skills'] as $value) {
            $userSkill = $this->userSkill->deleteUserSkill($id, $value['skill_id']);
        }
        return $userSkill;
    }

    /**
     * Display a listing of specified resources.
     *
     * @param int $userId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function userSkills(int $userId): Collection
    {
        $this->user->findOrFail($userId);
        return $this->userSkill->with('skill')->where('user_id', $userId)->get();
    }

    /**
     * Get username
     *
     * @param int $userId
     * @return string
     */
    public function getUserName(int $userId): string
    {
        return $this->user->getUserName($userId);
    }

    /**
     * List all the users
     *
     * @param int $userId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function listUsers(int $userId) : Collection
    {
        return $this->user->where('user_id', '<>', $userId)->get();
    }

    /**
     * Search user
     *
     * @param string $text
     * @param int $userId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function searchUsers(string $text = null, int $userId): Collection
    {
        return $this->user->searchUser($text, $userId)->get();
    }

    /**
     * Get user detail by email id
     *
     * @param string $email
     * @return App\User
     */
    public function getUserByEmail(string $email): User
    {
        $user = $this->user->getUserByEmail($email);
        
        if (is_null($user)) {
            throw new ModelNotFoundException(
                trans('messages.custom_error_message.ERROR_USER_NOT_FOUND')
            );
        }
        return $user;
    }

    /**
     *Add/Update user custom field value.
    *
    * @param array $userCustomFields
    * @param int $userId
    * @return null|App\Models\UserCustomFieldValue
    */
    public function updateCustomFields(array $userCustomFields, int $userId): ?UserCustomFieldValue
    {
        foreach ($userCustomFields as $data) {
            $userCustomFieldData = [
                'field_id' => $data['field_id'],
                'user_id' => $userId,
                'value' => $data['value']
            ];

            $userCustomField = $this->userCustomFieldValue->createOrUpdateCustomFieldValue(
                ['field_id' => $data['field_id'], 'user_id' => $userId],
                $userCustomFieldData
            );
            unset($userCustomFieldData);
        }
        return $userCustomField ?? null;
    }

    /**
     * Find specified resource in storage.
     *
     * @param  int  $id
     * @return App\User
     */
    public function findUserDetail(int $id): User
    {
        return $this->user->findUserDetail($id);
    }

    /**
     * Get Availability.
     *
     * @return Illuminate\Support\Collection
     */
    public function getAvailability(): SupportCollection
    {
        return $this->availability->getAvailability();
    }

    /**
     * Delete skills by userId
     *
     * @param int $userId
     * @return bool
     */
    public function deleteSkills(int $userId): bool
    {
        return $this->userSkill->deleteUserSkills($userId);
    }

    /**
     * Change user's password
     *
     * @param int $id
     * @param string $password
     *
     * @return bool
     */
    public function changePassword(int $id, string $password): bool
    {
        // Fetch user details from system and update password
        $userDetail = $this->user->find($id);
        $userDetail->password = $password;
        return $userDetail->save();
    }

    /**
     * Get user goal hours
     *
     * @param int $userId
     * @return null|string
     */
    public function getUserGoalHours(int $userId): ?string
    {
        return $this->user->getUserGoalHours($userId);
    }
}
