<?php

namespace App\Models;
use App\Models\MissionTabLanguage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MissionTab extends Model
{
    use softDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mission_tab';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'mission_id', 'sort_key'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'id', 'mission_id', 'sort_key', 'getMissionTabDetail'
    ];

    /**
     * Find the specified resource.
     * 
     * @return Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getMissionTabDetail()
    {
        return $this->hasMany(MissionTabLanguage::class, 'mission_tab_id', 'id');
    }
}
