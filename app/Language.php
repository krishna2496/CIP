<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    protected $table = 'language';

    protected $primaryKey = 'language_id';

    protected $fillable = ['name','code','status'];
    
    use SoftDeletes;
}
