<?php
namespace App\Repositories\User;

use App\Repositories\User\UserInterface;
use Illuminate\Http\{Request, Response};
use App\User;
use App\Helpers\{Helpers, ResponseHelper, DatabaseHelper};
use App\Models\UserSkill;
use Validator, PDOException, DB;

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
     * @var Illuminate\Http\Response
     */
    private $response;

    /**
     * Create a new User repository instance.
     *
     * @param  App\User $user
     * @param  Illuminate\Http\Response $response
     * @return void
     */
    public function __construct(User $user, UserSkill $userSkill, Response $response)
    {
        $this->user = $user;
        $this->response = $response;
        $this->userSkill = $userSkill;
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return User
     */
    public function store(Request $request): User
    {
        return $this->user->create($request->all());
    }
    
    /**
     * Get listing of users
     *
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
    public function userList(Request $request)
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return User
     */
    public function update(Request $request, int $id): User
    {
        $user = $this->user->findOrFail($id);
        $user->update($request->toArray());
        return $user;
    }
    
    /**
     * Find specified resource in storage.
     *
     * @param  int  $id
     * @return User
     */
    public function find(int $id): User
    {
        return $this->user->findUser($id);
    }
    
    /**
     * Remove specified resource in storage.
     *
     * @param  int  $id
     * @return mixed
     */
    public function delete(int $id)
    {
        return $this->user->deleteUser($id);
    }
    
    /**
     * Store a newly created resource into database
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function linkSkill(Request $request)
    {
        foreach ($request->skills as $value) {
            $skill = array(
                'user_id' => $request->user_id,
                'skill_id' => $value['skill_id'],
            );
            
			$this->userSkill->linkUserSkill($request->user_id, $value['skill_id']);
            unset($skill);
        }
		return true;
    }
    
    /**
     * Remove the specified resource from storage
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function unlinkSkill(Request $request)
    {
        $userSkill = $this->userSkill;
        foreach ($request->skills as $value) {
            $userSkill = $this->userSkill->deleteUserSkill($request->user_id, $value['skill_id']);
        }
        return $userSkill;
    }

    /**
     * Display a listing of specified resources.
     *
     * @param int $userId
     * @return mixed
     */
    public function userSkills(int $userId)
    {
        return $this->userSkill->find($user_id);   
    }
}
