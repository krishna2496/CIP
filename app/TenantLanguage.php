<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Language;

class TenantLanguage extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenant_language';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'tenant_language_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['tenant_id','language_id','default'];

    /**
     * The attributes that aappend in response.
     *
     * @var array
     */
    protected $appends = ['language_code'];

	/**
     * The attributes that are visible.
     *
     * @var array
     */
	protected $visible = ['language_id', 'default'];

    /**
     * Get the language record associated with the tenant language.
     */
    public function language()
    {
    	return $this->hasOne(Language::class, 'language_id', 'language_id');
    }
    /**
     * Get the language code from language.
     */
    public function getLanguageCodeAttribute()
    {
    	return $this->language->code;
    }
}
