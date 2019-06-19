<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use App\Models\Mission;

class MissionLanguage extends Model
{
    use SoftDeletes;

    protected $table = 'mission_language';
    protected $primaryKey = 'mission_language_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

	protected $fillable = ['mission_id', 'language_id', 'title', 'description', 'objective', 'short_description'];

    protected $visible = ['mission_language_id', 'lang', 'language_id', 'title', 'objective', 'short_description', 'description'];
	/**
     * Get the mission that has language titles.
     */
	public function mission()
    {
    	return $this->belongsTo(Mission::class, 'mission_id', 'mission_id');
    }
}
