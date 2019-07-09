<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Mission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavouriteMission extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'favourite_mission';

    /**
     * The primary key for the model.
     *
     * @var int
     */
    protected $primaryKey = 'favourite_mission_id';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mission_id', 'user_id'];
    
    /**
     * Get the mission that has media.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class, 'mission_id', 'mission_id');
    }
    
    /**
     * Soft delete from the database.
     *
     * @param  int  $id
     * @return void
     */
    public function deleteFavouriteMission(int $id)
    {
        $favouriteMission = static::findOrFail($id)->delete();
        return $favouriteMission;
    }
}
