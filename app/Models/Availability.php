<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Mission;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection as SupportCollection;

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
    protected $fillable = ['type'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['availability_id', 'type'];
    
    /**
     * Get the mission that has availability.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mission(): HasMany
    {
        return $this->hasMany(Mission::class, 'availability_id', 'availability_id');
    }

    /**
     * Get the mission that has availability.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'availability_id', 'availability_id');
    }
    
    /**
     * Get all resources.
     *
     * @return Illuminate\Support\Collection
     */
    public function getAvailability(): SupportCollection
    {
        return static::pluck('type', 'availability_id');
    }
}
