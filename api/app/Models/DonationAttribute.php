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

    // Donation related static code added,because donation related functionality is pending for development
    
    protected $appends = ['donation_amount_raised', 'donor_count', 'donation_count'];
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['goal_amount_currency', 'goal_amount', 'show_goal_amount', 'show_donation_percentage', 'show_donation_meter', 'show_donation_count',
    'show_donors_count', 'disable_when_funded', 'is_disabled', 'donation_amount_raised', 'donor_count', 'donation_count'];

    public function getDonationAmountRaisedAttribute()
    {
        return 358;
    }

    public function getDonorCountAttribute()
    {
        return 21;
    }

    public function getDonationCountAttribute()
    {
        return 78;
    }
}
