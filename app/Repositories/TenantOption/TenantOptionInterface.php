<?php
namespace App\Repositories\TenantOption;

use Illuminate\Http\Request;

interface TenantOptionInterface
{

    /**
     * Store tenant slider data.
     *
     * @param  array $data
     * @return void
     */
    public function storeSlider(array $data);

    /**
     * Update tenant styling settings data.
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function updateStyleSettings(Request $request);

    /**
     *  Get all sliders of tenant.
     *
     * @return void
     */
    public function getAllSlider();
}
