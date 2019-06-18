<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FooterPagesLanguage;

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
	
	protected $visible = ['page_id', 'status', 'slug', 'pageLanguages'];
	
    public function pageLanguages()
    {
    	return $this->hasMany(FooterPagesLanguage::class, 'page_id', 'page_id');
    }
	
	/**
     * Delete the specified resource.
     *
     * @param  int  $id
     * @return array
     */
    public function deleteFooterPage(int $id)
    {
        return static::findOrFail($id)->delete();
    }

}
