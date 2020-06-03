<?php
namespace App\Repositories\UserCustomField;

use App\Repositories\UserCustomField\UserCustomFieldInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Models\UserCustomField;

class UserCustomFieldRepository implements UserCustomFieldInterface
{
    /**
     * User custom field
     *
     * @var App\Models\UserCustomField
     */
    private $field;
    
    /**
     * Create a new repository instance.
     *
     * @param App\Models\UserCustomField $field
     * @return void
     */
    public function __construct(UserCustomField $field)
    {
        $this->field = $field;
    }
    
    /**
     * Get listing of user custom fields
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function userCustomFieldList(Request $request): LengthAwarePaginator
    {
        $customFields = $this->field;
        if ($request->has('search')) {
            $customFields = $customFields->where('name', 'like', '%' . $request->input('search') . '%')
                ->orWhere('internal_note', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('type')) {
            $customFields = $customFields->whereIn('type', $request->input('type'));
        }

        if ($request->has('mandatory')) {
            $customFields = $customFields->where('is_mandatory', $request->input('mandatory'));
        }
        
        if ($request->has('order')) {
            $orderDirection = $request->input('order', 'asc');
            $customFields = $customFields->orderBy('field_id', $orderDirection);
        }

        return $customFields->paginate($request->perPage);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param array $request
     * @return App\Models\UserCustomField
     */
    public function store(array $request): UserCustomField
    {
        return $this->field->create($request);
    }
    
    /**
    * Update the specified resource in storage.
    *
    * @param  array  $request
    * @param  int  $id
    * @return App\Models\UserCustomField
    */
    public function update(array $request, int $id): UserCustomField
    {
        $customField = $this->field->findOrFail($id);
        $customField->update($request);
        return $customField;
    }
    
    /**
     * Find user custom field in storage.
     *
     * @param  int  $id
     * @return App\Models\UserCustomField
     */
    public function find(int $id): UserCustomField
    {
        return $this->field->findOrFail($id);
    }
       
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->field->deleteCustomField($id);
    }

    /**
     * Get listing of user custom fields
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Support\Collection
     */
    public function getUserCustomFields(Request $request): Collection
    {
        return $this->field->get();
    }
}
