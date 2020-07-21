<?php
namespace App\Repositories\UnitedNationSDG;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;

interface UnitedNationSDGInterface
{
    /**
     * Display a listing of the United Nation SDG.
     *
     */
    public function find(): Collection;

    /**
     * Store United Nation SDG.
     *
     */
    public function addUnSdg(int $missionId, Request $request);

    /**
     * Update United Nation SDG.
     *
     */
    public function updateUnSdg(int $missionId, Request $request);
}
