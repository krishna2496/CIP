<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\UserSkill;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'skill';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'skill_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['skill_id', 'skill_name', 'translations', 'parent'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['skill_name', 'translations', 'parent_skill'];

    /**
     * Set translations attribute on the model.
     *
     * @param  array $value
     * @return void
     */
    public function setTranslationsAttribute(array $value): void
    {
        $this->attributes['translations'] = json_encode($value);
    }
    
    /**
     * Get an attribute from the model.
     *
     * @param  string $value
     * @return null|array
     */
    public function getTranslationsAttribute(string $value): ?array
    {
        return json_decode($value, true);
    }
    
    /**
     * Find the specified resource.
     *
     * @param  int  $id
     * @return array
     */
    public function findSkill(int $id): Skill
    {
        return static::with('parent')->findOrFail($id);
    }

    /**
     * Delete the specified resource.
     *
     * @param  int  $id
     * @return bool
     */
    public function deleteSkill(int $id): bool
    {
        return static::findOrFail($id)->delete();
    }
    
    /**
     * Find the parent resource.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_skill');
    }
}
