<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\NewsLanguage;

class News extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'news';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'news_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['news_image', 'user_name', 'user_title', 'user_thumbnail', 'status'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = [
        'news_id',
        'news_image',
        'user_name',
        'user_title',
        'user_thumbnail',
        'newsLanguage',
        'newsToCategory',
		'created_at'
    ];

    /**
     * Find news by id.
     *
     * @param  int  $id
     * @return News
     */
    public function findNews(int $id): News
    {
        return static::findOrFail($id);
    }
    
    /**
     * Delete news category by id.
     *
     * @param  int  $id
     * @return bool
     */
    public function deleteNews(int $id): bool
    {
        return static::findOrFail($id)->delete();
    }

    /**
     * Get news Language
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function newsLanguage(): HasMany
    {
        return $this->hasMany(NewsLanguage::class, 'news_id', 'news_id');
    }

    /**
     * Get news to category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function newsToCategory(): HasMany
    {
        return $this->hasMany(NewsToCategory::class, 'news_id', 'news_id');
    }
}
