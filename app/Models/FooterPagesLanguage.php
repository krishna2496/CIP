<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use App\Models\FooterPage;

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
    
    protected $visible = ['language_id', 'title', 'description'];
    
    public function page()
    {
        return $this->belongsTo(FooterPage::class, 'page_id', 'page_id');
    }
    
    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = serialize($value);
    }
    
    public function getDescriptionAttribute($value)
    {
        return unserialize($value);
    }
}
