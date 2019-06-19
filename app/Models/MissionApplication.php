<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use App\Models\Mission;

class MissionApplication extends Model
{
    use SoftDeletes;

    protected $table = 'mission_application';
    protected $primaryKey = 'mission_application_id';

    /**
     * Defined relation for the mission table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mission()
    {
        return $this->belongsTo(Mission::class, 'mission_id', 'mission_id');
    }
}
