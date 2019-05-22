<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model {

	protected $table = 'tenant';

	protected $primaryKey = 'tenant_id';
	
    protected $fillable = ['name','sponsor_id'];

    protected $dates = ['created_at','updated_at','deleted_at'];

    public static $rules = [
        // Validation rules
    ];

    // Relationships

}
