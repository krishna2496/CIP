<?php
namespace App\Repositories\VolunteeringAttribute;

use App\Repositories\VolunteeringAttribute\VolunteeringAttributeInterface;
use Illuminate\Http\Request;
use App\Models\VolunteeringAttribute;
use App\Traits\RestExceptionHandlerTrait;

class VolunteeringAttributeRepository implements VolunteeringAttributeInterface
{
    use RestExceptionHandlerTrait;
    
    /**
     * Create a new user filter repository instance.
     *
     * @param  App\Models\VolunteeringAttribute $filters
     * @return void
     */
    public function __construct(VolunteeringAttribute $filters)
    {
        
    }
}
