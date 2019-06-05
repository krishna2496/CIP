<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'language';

    protected $primaryKey = 'language_id';

    protected $fillable = ['name','code','status'];
}
