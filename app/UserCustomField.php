<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCustomField extends Model
{
    protected $table = 'user_custom_field';
    protected $primaryKey = 'field_id';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'type', 'translations', 'is_mandatory'];

}
