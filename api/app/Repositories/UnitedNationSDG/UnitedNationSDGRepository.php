<?php
namespace App\Repositories\UnitedNationSDG;

use App\Models\UnitedNationSDG;
use Illuminate\Support\Collection;

class UnitedNationSDGRepository implements UnitedNationSDGInterface
{
    /**
     * Display a listing of the United Nation SDG.
     * 
     * @param App\Models\UnitedNationSDG
     */
    public function find(): Collection
    {
        $return = [];
        $allUnSdg = config('constants.UN_SDG');
        foreach ($allUnSdg as $key => $value) {
            $return[] = new UnitedNationSDG($key, $value);
        }
        return collect($return);        
    }
}
