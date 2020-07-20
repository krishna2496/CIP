<?php
namespace App\Models;
 
class UnitedNationSDG{
    
    //The number of UN SDG.
    public $number;
    
    //The UN SDG
    public $unSdg;

    /**
     * Create a new United Nation SDG instance.
     *
     * @param int $number
     * @param string $unSdg
     * @return void
     */

    public function __construct(int $number, string $unSdg)
    {
        $this->number = $number;
        $this->unSdg = $unSdg;
    }
    
}