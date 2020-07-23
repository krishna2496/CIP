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
        return collect([
            new UnitedNationSDG(1, 'No poverty'),
            new UnitedNationSDG(2, 'Zero hunger'),
            new UnitedNationSDG(3, 'Good Health and Well-being'),
            new UnitedNationSDG(4, 'Quality Education'),
            new UnitedNationSDG(5, 'Gender Equality'),
            new UnitedNationSDG(6, 'Clean Water and Sanitation'),
            new UnitedNationSDG(7, 'Affordable and Clean Energy'),
            new UnitedNationSDG(8, 'Decent Work and Economic Growth'),
            new UnitedNationSDG(9, 'Industry, Innovation and Infrastructure'),
            new UnitedNationSDG(10, 'Reducing Inequality'),
            new UnitedNationSDG(11, 'Sustainable Cities and Communities'),
            new UnitedNationSDG(12, 'Responsible Consumption and Production'),
            new UnitedNationSDG(13, 'Climate Action'),
            new UnitedNationSDG(14, 'Life Below Water'),
            new UnitedNationSDG(15, 'Life on Land'),
            new UnitedNationSDG(16, 'Peace, Justice, and Strong Institutions'),
            new UnitedNationSDG(17, 'Partnerships for the Goals')
        ]);
    }
}
