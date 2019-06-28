<?php
namespace App\Repositories\User;

use Illuminate\Database\Eloquent\Collection;
use App\Repositories\User\UserInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use App\User;
use App\Helpers\Helpers;
use App\Helpers\ResponseHelper;
use App\Helpers\DatabaseHelper;
use App\Models\UserSkill;
use Validator;
use PDOException;
use DB;

class UserRepository implements UserInterface
{
    /**
     * @var App\User
     */
    public $user;

    /**
     * @var App\Model\UserSkill
     */
    public $userSkill;
    
    /**
     * @var App\Helpers\ResponseHelper
     */
    private $responseHelper;

    /**
     * Create a new User repository instance.
     *
     * @param  App\User $user
     * @param  Illuminate\Http\ResponseHelper $responseHelper
     * @return void
     */
    public function __construct(User $user, UserSkill $userSkill, ResponseHelper $responseHelper)
    {
        $this->user = $user;
        $this->responseHelper = $responseHelper;
        $this->userSkill = $userSkill;
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
        $userQuery = $this->user->with('city', 'country', 'timezone');
        
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
        return $userQuery->paginate(config('constants.PER_PAGE_LIMIT'));
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
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function linkSkill($request): bool
    {
        foreach ($request['skills'] as $value) {
            $skill = array(
                'user_id' => $request['user_id'],
                'skill_id' => $value['skill_id'],
            );
            
            $this->userSkill->linkUserSkill($request['user_id'], $value['skill_id']);
            unset($skill);
        }
        return true;
    }
    
    /**
     * Remove the specified resource from storage
     *
     * @param array $request
     * @return bool
     */
    public function unlinkSkill($request): bool
    {
        $userSkill = $this->userSkill;
        foreach ($request['skills'] as $value) {
            $userSkill = $this->userSkill->deleteUserSkill($request['user_id'], $value['skill_id']);
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
        return $this->userSkill->with('skill')->where('user_id', $userId)->get();
    }
}
