<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Mission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionRating extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mission_rating';

    /**
     * The primary key for the model.
     *
     * @var int
     */
    protected $primaryKey = 'mission_rating_id';
    
    /**
     * Get the mission that has media.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class, 'mission_id', 'mission_id');
    }
}
