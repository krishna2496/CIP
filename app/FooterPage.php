<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FooterPage extends Model
{
    protected $table = 'footer_page';
    protected $primaryKey = 'page_id';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['status'];

    public function pageLanguages()
    {
    	return $this->hasMany(FooterPagesLanguage::class);
    }
}
