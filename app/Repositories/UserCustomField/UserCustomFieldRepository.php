<?php
namespace App\Repositories\UserCustomField;

use App\Repositories\UserCustomField\UserCustomFieldInterface;
use Illuminate\Http\Request;
use App\Models\UserCustomField;
use DB;

class UserCustomFieldRepository implements UserCustomFieldInterface
{
	/**
     * User custom field
     *
     * @var UserCustomField
     */
    private $field;
	
	/**
     * Create a new repository instance.
     *
     * @return void
     */
    function __construct(UserCustomField $field) {
		$this->field = $field;
    }
	
	/**
     * Get listing of user custom fields
     *
     * @param Illuminate\Http\Request $request
     * @return mixed
     */
	public function UserCustomFieldList(Request $request) 
	{
		$customFields = $this->field;
		if ($request->has('search')) {
			$customFields = $customFields->where('name', 'like', '%' . $request->input('search') . '%');
		}
		
		if ($request->has('order')) {
			$orderDirection = $request->input('order', 'asc');
			$customFields = $customFields->orderBy('field_id', $orderDirection);
		}			
		return $customFields->paginate(config('constants.PER_PAGE_LIMIT'));
	}
	
	/**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
	public function store(Request $request): UserCustomField
    {
		$customField = $this->field->create($request->all());
		return $customField;
	}
	
	 /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return mixed
     */
	public function update(Request $request, int $id): UserCustomField
	{
		$customField = $this->field->findOrFail($id);
		$customField->update($request->all());
		return $customField;		
	}
	
	
	/**
     * Find the specified resource in storage.
     *
     * @param  int  $id
     * @return mixed
     */
    public function find(int $id) {
		
		
	}
	
	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return mixed
     */
    public function delete(int $id) {
		return $this->field->deleteCustomField($id);
	}
}