<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteeringAttribute extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'volunteering_attribute';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'volunteering_attribute_id';

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['volunteering_attribute_id', 'mission_id,', 'availability_id',
            'total_seats', 'is_virtual'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['volunteering_attribute_id', 'mission_id,', 'availability_id',
        'total_seats', 'is_virtual'
    ];
}
