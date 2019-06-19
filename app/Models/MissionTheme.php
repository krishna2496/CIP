<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use App\Models\Mission;

class MissionTheme extends Model
{
    use SoftDeletes;

    protected $table = 'mission_theme';
    protected $primaryKey = 'mission_theme_id';

    protected $visible = ['mission_theme_id', 'theme_name', 'translations'];

    /**
     * Get the mission that has theme
     */
    public function mission()
    {
    	return $this->hasMany(Mission::class, 'mission_theme_id', 'theme_id'); 
    }
}
