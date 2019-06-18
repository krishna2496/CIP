<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use App\Models\FooterPage;

class FooterPagesLanguage extends Model
{
	use SoftDeletes;

    protected $table = 'footer_pages_language';
    protected $primaryKey = 'id';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['page_id', 'language_id', 'title', 'description'];
	
    protected $visible = ['page_id', 'language_id', 'title', 'description'];

    public function page()
    {
    	return $this->belongsTo(FooterPage::class, 'page_id', 'page_id');
    }
}
