<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use App\Models\FooterPagesLanguage;

class FooterPage extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'footer_page';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'page_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['status', 'slug'];

    /**
     * Defined has many relation for the footer_page_language table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pageLanguages()
    {
        return $this->hasMany(FooterPagesLanguage::class, 'page_id', 'page_id');
    }
}
