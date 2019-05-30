<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\FooterPage;

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

    public function page()
    {
    	return $this->belongsTo(FooterPage::class, 'page_id', 'page_id');
    }
}
