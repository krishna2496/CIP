<?php

namespace App\Repositories\User;

use App\Repositories\User\UserInterface;
use Illuminate\Http\{Request, Response};
use PDOException;
use App\User;

class UserRepository implements UserInterface
{
    public $user;
	
	private $response;

    function __construct(User $user, Response $response) {
		$this->user = $user;
		$this->response = $response;
    }
	
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
		} catch(\InvalidArgumentException $e) {
			throw new \InvalidArgumentException($e->getMessage());
		}
	}

    public function find(int $id) 
	{
		return $this->user->findUser($id);
	}
	
    public function delete(int $id) {
		
		try {
			
			$this->user->deleteUser($id);
			
			// Set response data
			$apiStatus = $this->response->status();            
			$apiMessage = trans('messages.success.MESSAGE_USER_DELETED');

			return ResponseHelper::success($apiStatus, $apiMessage);
			
		} catch(ModelNotFoundException $e){
			
			throw new ModelNotFoundException(trans('messages.custom_error_message.100000'));
			
        }
	}
}