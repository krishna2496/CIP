<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notifications extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'notification_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['notification_id', 'notification_type_id',
    'user_id', 'is_read', 'entity_id', 'action'];
}
