<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};

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
	
	protected $visible = ['field_id', 'name', 'type', 'translations', 'is_mandatory'];
	
	public function setTranslationsAttribute($value)
    {
		$this->attributes['translations'] = serialize($value);
    }
	
	public function getTranslationsAttribute($value)
    {
        return unserialize($value);
    }
	
	/**
     * Delete the specified resource.
     *
     * @param  int  $id
     * @return array
     */
    public function deleteCustomField(int $id)
    {
        return static::findOrFail($id)->delete();
    }

}
