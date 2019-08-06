<?php

namespace App\Http\Controllers\App\Mission;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\Helpers;
use App\Models\Mission;
use Illuminate\View\View;

class MissionSocialSharingController extends Controller
{
    
    /**
     * @var App\Helpers\Helpers
     */
    private $helpers;

    /**
     * Create a new controller instance.
     *
     * @param  App\Helpers\Helpers $helpers
     * @return void
     */
    public function __construct(Helpers $helpers)
    {
        $this->helpers = $helpers;
    }

    /**
     * Set meta data for social sharing page
     *
     * @param  string $fqdn
     * @param  int $missionId
     * @param  int $langId
     * @return Illuminate\View\View
     */
    public function setMetaData(string $fqdn, int $missionId, int $langId): View
    {
        // Need to get tenant id from tenant name        
        $tenant = $this->helpers->getTenantDetailsFromName($fqdn);

        // Get mission details from mission id
        $mission = Mission::where('mission_id', $missionId)
        ->with(
            [
                'missionLanguage' => function ($q) use ($langId) {
                    $q->where('language_id', $langId);
                },
                'missionMedia' => function ($q) {
                    $q->where('default', 1);
                }
            ]
        )
        ->first();
        
        return view('social-share', compact('mission', 'fqdn', 'missionId', 'langId'));
    }
}
