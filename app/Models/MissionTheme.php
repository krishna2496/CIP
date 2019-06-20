<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use App\Models\Mission;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MissionTheme extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mission_theme';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'mission_theme_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['mission_theme_id', 'theme_name', 'translations'];

    /**
     * Get the mission that has theme
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mission(): HasMany
    {
        return $this->hasMany(Mission::class, 'mission_theme_id', 'theme_id');
    }
}
