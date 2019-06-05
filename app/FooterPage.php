<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\FooterPagesLanguage;

class FooterPage extends Model
{
	use SoftDeletes;

    protected $table = 'footer_page';
    protected $primaryKey = 'page_id';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['status', 'slug'];

    public function pageLanguages()
    {
    	return $this->hasMany(FooterPagesLanguage::class, 'page_id', 'page_id');
    }

}
