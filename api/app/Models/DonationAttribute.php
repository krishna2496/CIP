<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonationAttribute extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'donation_attribute';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'donation_attribute_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mission_id', 'goal_amount_currency', 'goal_amount', 'show_goal_amount', 'show_donation_percentage', 'show_donation_meter', 'show_donation_count',
    'show_donors_count', 'disable_when_funded', 'is_disabled', ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['goal_amount_currency', 'goal_amount', 'show_goal_amount', 'show_donation_percentage', 'show_donation_meter', 'show_donation_count',
    'show_donors_count', 'disable_when_funded', 'is_disabled', ];
}
