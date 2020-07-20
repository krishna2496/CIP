<?php
namespace App\Repositories\UnitedNationSDG;

use Illuminate\Support\Collection;

interface UnitedNationSDGRInterface
{
    /**
     * Display a listing of the United Nation SDG.
     *
     */
    public function find(): Collection;
}
