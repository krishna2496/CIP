<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Mission;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection as SupportCollection;
use App\User;

class Availability extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'availability';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'availability_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type','translations'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['availability_id', 'type', 'translations'];
            
    /**
     * Get all resources.
     *
     * @return Illuminate\Support\Collection
     */
    public function getAvailability(): SupportCollection
    {
        return static::select('translations', 'availability_id')->get();
    }
    
    /**
     * Set translations attribute on the model.
     *
     * @param  array $value
     * @return void
     */
    public function setTranslationsAttribute(array $value): void
    {
        $this->attributes['translations'] = serialize($value);
    }
    
    /**
     * Get an attribute from the model.
     *
     * @param  string $value
     * @return array
     */
    public function getTranslationsAttribute(string $value): array
    {
        return unserialize($value);
    }
    
    /**
     * Delete availability details.
     *
     * @param  int  $id
     * @return bool
     */
    public function deleteAvailability(int $id): bool
    {
        return static::findOrFail($id)->delete();
    }

    /**
     * Get the mission which belongs to availability
     *
     * @return void
     */
    public function mission()
    {
        return $this->belongsTo(Mission::class, 'availability_id', 'availability_id');
    }

    /**
     * Get the user which belongs to availability
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'availability_id', 'availability_id');
    }
}
