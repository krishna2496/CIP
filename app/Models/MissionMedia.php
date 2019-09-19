<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Mission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    protected $fillable = ['mission_id', 'media_type', 'media_name', 'media_path', 'default'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['mission_media_id', 'media_type', 'media_name', 'media_path', 'default', 'media_image'];
    
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

    /**
     * Return youtube thumbnail from video URL
     * @codeCoverageIgnore
     * @return string|null
     */
    public function getMediaImageAttribute(): ?string
    {
        if ($this->attributes['media_type'] == 'mp4') {
            preg_match(
                '/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/',
                $this->attributes['media_path'],
                $matches
            );
            if (count($matches)) {
                return "https://img.youtube.com/vi/".$matches[2]."/mqdefault.jpg";
            }
        }
        if ($this->attributes['media_type'] !== 'mp4' && !is_null($this->attributes['media_type'])) {
            return $this->attributes['media_path'];
        }
        return null;
    }
}
