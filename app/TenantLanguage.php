<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Language;

class TenantLanguage extends Model
{
    protected $table = 'tenant_language';

    protected $primaryKey = 'tenant_language_id';

    protected $fillable = ['tenant_id','language_id','default'];

    protected $appends = ['language_code'];

    use SoftDeletes;

    public function language()
    {
    	return $this->hasOne(Language::class, 'language_id', 'language_id');
    }
    public function getLanguageCodeAttribute()
    {
    	return $this->language->code;
    }
}
