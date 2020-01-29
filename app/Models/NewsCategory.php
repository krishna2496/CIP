<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsCategory extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'news_category';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'news_category_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['news_category_id', 'category_name', 'translations'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['category_name', 'translations'];

    /**
     * Set translations attribute on the model.
     *
     * @param  array $value
     * @return void
     */
    public function setTranslationsAttribute(array $value): void
    {
        $this->attributes['translations'] = serialize($value);
    }
    
    /**
     * Get an attribute from the model.
     *
     * @param  string $value
     * @return array
     */
    public function getTranslationsAttribute(string $value): array
    {
        $data = @unserialize($value);
        return ($data !== false) ? unserialize($value) : [];
    }

    /**
     * Find news category by id.
     *
     * @param  int  $id
     * @return NewsCategory
     */
    public function findNewsCategory(int $id): NewsCategory
    {
        return static::findOrFail($id);
    }
    
    /**
     * Delete news category by id.
     *
     * @param  int  $id
     * @return bool
     */
    public function deleteNewsCategory(int $id): bool
    {
        return static::findOrFail($id)->delete();
    }
}
