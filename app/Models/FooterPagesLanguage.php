<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FooterPage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FooterPagesLanguage extends Model
{
    use SoftDeletes;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'footer_pages_language';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['page_id', 'language_id', 'title', 'description'];
    
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['page_id', 'language_id', 'title', 'description', 'sections'];
    
    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(FooterPage::class, 'page_id', 'page_id');
    }
    
    /**
     * Set description attribute on the model.
     *
     * @param  mixed   $value
     * @return void
     */
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = serialize($value);
    }
    
    /**
     * Get an attribute from the model.
     *
     * @param  string  $value
     * @return mixed
     */
    public function getDescriptionAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $value
     * @return mixed
     */
    public function getSectionsAttribute($value)
    {
        return unserialize($value);
    }
}
