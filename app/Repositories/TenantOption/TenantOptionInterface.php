<?php
namespace App\Repositories\TenantOption;

use Illuminate\Http\Request;
use App\Models\TenantOption;
use Illuminate\Database\Eloquent\Collection;

interface TenantOptionInterface
{
    /**
     * Store tenant slider data.
     *
     * @param  array $data
     * @return App\Models\TenantOption
     */
    public function storeSlider(array $data): TenantOption;

    /**
     * Update style settings.
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function updateStyleSettings(Request $request);

    /**
     * Get a listing of slider.
     *
     * @return Illiminate\Support\Collection
     */
    public function getAllSlider();

    /**
     * Store tenant option data
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getOptions(): Collection;

    /**
    * Get a listing of resource.
    *
    * @param array $conditions
    * @return null|App\Models\TenantOption
    */
    public function getOptionWithCondition(array $conditions = []): ?TenantOption;

    /**
    * Get a count of slider.
    *
    * @return int
    */
    public function getAllSliderCount(): int;

    /**
     * Create new option
     *
     * @param array $option
     * @return App\Models\TenantOption
     */
    public function store(array $option): TenantOption;

    /**
     * Select by option name
     *
     * @param String $data
     * @return Illuminate\Support\Collection
     */
    public function getOptionValue(string $data);
}
