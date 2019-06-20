<?php

namespace App\Repositories\UserCustomField;

use App\Repositories\UserCustomField\UserCustomFieldInterface;
use Illuminate\Http\{Request, Response};
use DB;
use App\Models\UserCustomField;
use App\Helpers\{Helpers, LanguageHelper};

class UserCustomFieldRepository implements UserCustomFieldInterface
{
    private $field;

    function __construct(UserCustomField $field) {
		$this->field = $field;
    }
	
	public function store(Request $request)
    {
		$customField = $this->field->create($request->all());
		return $customField;
	}
	
	public function update(Request $request, int $id) 
	{
		$customField = $this->field->findOrFail($id);
		$customField->update($request->all());
		return $customField;		
	}
	
	public function UserCustomFieldList(Request $request) 
	{
		try {
			$customFields = $this->field;
            if ($request->has('search')) {
				$customFields = $customFields->where('name', 'like', '%' . $request->input('search') . '%');
			}
			
            if ($request->has('order')) {
                $orderDirection = $request->input('order', 'asc');
                $customFields = $customFields->field->orderBy('field_id', $orderDirection);
            }			
            return $customFields->paginate(config('constants.PER_PAGE_LIMIT'));
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }	
	}
	
    public function find(int $id) {
		
		
	}
	
    public function delete(int $id) {
		return $this->field->deleteCustomField($id);
	}
}