<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MissionMedia extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mission_media';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'mission_media_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mission_id', 'media_type', 'media_name', 'media_path', 'default', 'sort_order'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['mission_media_id', 'media_type', 'media_name', 'media_path', 'default', 'sort_order'];
    
    protected $appends = ['video_thumbnail'];

    /**
     * Store/update specified resource.
     *
     * @param  array $condition
     * @param  array $data
     * @return App\Models\MissionMedia
     */
    public function createOrUpdateMedia(array $condition, array $data): MissionMedia
    {
        return static::updateOrCreate($condition, $data);
    }
}
