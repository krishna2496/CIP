<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiUser extends Model
{
    protected $table = 'api_user';

    protected $primaryKey = 'api_user_id';

    protected $fillable = ['tenant_id','api_key','api_secret','status'];

    protected $dates = ['created_at','updated_at','deleted_at'];

    use SoftDeletes;
}
