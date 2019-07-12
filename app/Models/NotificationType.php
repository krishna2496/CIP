<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationType extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notification_type';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'notification_type_id';
}
