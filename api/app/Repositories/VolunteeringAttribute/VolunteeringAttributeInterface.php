<?php
namespace App\Repositories\VolunteeringAttribute;

use Illuminate\Http\Request;
use App\Models\VolunteeringAttribute;

interface VolunteeringAttributeInterface
{
    /**
     * Display a listing of volunteering attribute.
     *
     * @param Illuminate\Http\Request $request
     * @return null|App\Models\VolunteeringAttribute
     */
    public function volunteeringAttribute(Request $request): ?VolunteeringAttribute;
}
