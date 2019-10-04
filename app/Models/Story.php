<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\User;

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
    protected $visible = ['story_id', 'user_id', 'mission_id', 'title', 'description', 'status', 'published_at', 'created_at', 'mission_title', 'mission_description', 'first_name', 'last_name','avatar','why_i_volunteer','profile_text','storyMedia','user','mission','missionTheme', 'city', 'country'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'user_id', 'mission_id','title','description','status','published_at'];
    
    /**
     * Defined has one relation for the user table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user(): HasOne
    {
    	return $this->hasOne(User::class, 'user_id', 'user_id');
    }
    
    /**
     * Defined has one relation for the mission table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function mission(): HasOne
    {
    	return $this->hasOne(Mission::class, 'mission_id', 'mission_id');
    }
    
    /**
     * Get the media record associated with the story.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function storyMedia(): HasMany
    {
    	return $this->hasMany(StoryMedia::class, 'story_id', 'story_id');
    }
    
    /**
     * Soft delete from the database.
     *
     * @param  int  $id
     * @return bool
     */
    public function deleteStory(int $id): bool
    {
    	return static::findOrFail($id)->delete();
    }
}
