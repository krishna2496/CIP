<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use App\Models\Mission;

class MissionMedia extends Model
{
    use SoftDeletes;

    protected $table = 'mission_media';
    protected $primaryKey = 'mission_media_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

	protected $fillable = ['mission_id', 'media_type', 'media_name', 'media_path', 'default'];

    protected $visible = ['mission_media_id', 'media_type', 'media_name', 'media_path', 'default'];
	/**
     * Get the mission that has media.
     */
	public function mission()
    {
    	return $this->belongsTo(Mission::class, 'mission_id', 'mission_id');
    }
}
