<?php
namespace App\Repositories\User;

use App\Repositories\User\UserInterface;
use Illuminate\Http\{Request, Response};
use App\Helpers\{Helpers, ResponseHelper};
use App\User;
use App\Models\UserSkill;
use Validator, PDOException, DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * Store a newly created resource into database
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $user = $this->user->create($request->all());
        return $user;
    }
    
    public function update(Request $request, int $id)
    {
        $user = $this->user->findOrFail($id);
        $user->update($request->toArray());

        return $user;
    }
    
    public function userList(Request $request)
    {
        try {
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
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }
    }

    public function find(int $id)
    {
        return $this->user->findUser($id);
    }
    
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
        $userSkill = $this->userSkill;
        foreach ($request->skills as $value) {
            $skill = array(
                'user_id' => $request->user_id,
                'skill_id' => $value['skill_id'],
            );
            
            $skillData = $this->userSkill->findUserSkill($request->user_id, $value['skill_id']);
            if (count($skillData) < 1) {
                $userSkill = $this->userSkill->create($skill);
            }
            unset($skill);
        }
        return $userSkill;
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
        $userSkill = $this->userSkill->find($userId);
        return $userSkill;
    }
}
