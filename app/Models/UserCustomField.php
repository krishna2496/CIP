<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\UserCustomFieldValue;

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
     * @param  mixed $value
     * @return void
     */
    public function setTranslationsAttribute(array $value): void
    {
        $this->attributes['translations'] = json_encode($value);
    }
    
    /**
     * Get an attribute from the model.
     *
     * @param  string $value
     * @return array
     */
    public function getTranslationsAttribute(string $value): ?array
    {
        return json_decode($value, true);
    }
    
    /**
     * Delete the specified resource.
     *
     * @param  int $id
     * @return bool
     */
    public function deleteCustomField(int $id): bool
    {
        return static::findOrFail($id)->delete();
    }
}
