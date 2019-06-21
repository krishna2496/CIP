<?php
namespace App\Repositories\User;

use App\Repositories\User\UserInterface;
use Illuminate\Http\{Request, Response};
use PDOException;
use App\User;
use App\Helpers\{Helpers, ResponseHelper, DatabaseHelper};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class UserRepository implements UserInterface
{
	/**
     * @var User
     */
    public $user;
	
	/**
     * @var Response
     */
	private $response;
	
	/**
     * Create a new repository instance.
     *
     * @return void
     */
    function __construct(User $user, Response $response) {
		$this->user = $user;
		$this->response = $response;
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
			$userQuery->where(function($query) use($request) {
				$query->orWhere('first_name', 'like', '%' . $request->input('search') . '%');
				$query->orWhere('last_name', 'like', '%' . $request->input('search') . '%');
			});
		}
		if ($request->has('order')) {
			$orderDirection = $request->input('order','asc');
			$userQuery->orderBy('user_id', $orderDirection);
		}
		return $userQuery->paginate(config('constants.PER_PAGE_LIMIT'));
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
}