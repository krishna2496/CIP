<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use  App\Models\MissionImpactDonationLanguage;
use Iatstuti\Database\Support\CascadeSoftDeletes;

class MissionImpactDonation extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mission_impact_donation';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'mission_impact_donation_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['mission_impact_donation_id', 'mission_id,', 'amount', 'missionImpactDonationDetail'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mission_impact_donation_id', 'mission_id', 'amount'];

    /*
     * Iatstuti\Database\Support\CascadeSoftDeletes;
     */
    protected $cascadeDeletes = ['missionImpactDonationDetail'];

    /**
     * Find the specified resource.
     * 
     */
    public function missionImpactDonationDetail()
    {
        return $this->hasMany(MissionImpactDonationLanguage::class, 'impact_donation_id', 'mission_impact_donation_id');
    }
}
