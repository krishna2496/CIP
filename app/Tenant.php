<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model {

    protected $fillable = ['tenant_name','sponsor_id','skills_enabled','themes_enabled','stories_enabled','news_enabled'];

    protected $dates = ['created_at','updated_at','deleted_at'];

    public static $rules = [
        // Validation rules
    ];

    // Relationships

}
