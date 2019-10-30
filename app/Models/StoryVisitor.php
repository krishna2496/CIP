<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoryVisitor extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'story_visitor';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'story_visitor_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['story_visitor_id', 'user_id', 'story_id', 'created_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'story_id'];

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
     * Defined has one relation for the story table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function story(): HasOne
    {
        return $this->hasOne(Story::class, 'story_id', 'story_id');
    }
}
