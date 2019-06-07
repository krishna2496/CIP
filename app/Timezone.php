<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timezone extends Model
{
    protected $table = 'timezone';
    protected $primaryKey = 'timezone_id';

    use SoftDeletes;
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['timezone_id', 'timezone', 'offset', 'status'];
}
