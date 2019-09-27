<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Story extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'story';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'story_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['story_id', 'user_id', 'mission_id', 'title' , 'description', 'status', 'published_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'mission_id', 'title' , 'description', 'status', 'published_at'];
    
    /**
     * Set description attribute on the model.
     *
     * @param string $value
     * @return void
     */
    public function setDescriptionAttribute(string $value)
    {
        $this->attributes['description'] = trim($value);
    }
}
