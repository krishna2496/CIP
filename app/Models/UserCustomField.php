<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCustomField extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
	protected $table = 'user_custom_field';
	
	/**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'field_id';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'type', 'translations', 'is_mandatory'];
	
	/**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
	protected $visible = ['field_id', 'name', 'type', 'translations', 'is_mandatory'];
	
	/**
	 * Set translations attribute on the model.
	 *
	 * @param  mixed   $value
	 * @return void
	 */
	public function setTranslationsAttribute(array $value): void
    {
		$this->attributes['translations'] = serialize($value);
    }
	
	/**
	 * Get an attribute from the model.
	 *
	 * @param  string  $value
	 * @return array
	 */
	public function getTranslationsAttribute(string $value): array
    {
        return unserialize($value);
    }
	
	/**
     * Delete the specified resource.
     *
     * @param  int  $id
     * @return void
     */
    public function deleteCustomField(int $id)
    {
        return static::findOrFail($id)->delete();
    }

}