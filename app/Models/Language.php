<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};

class Language extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'language';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'language_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','code','status'];
}
