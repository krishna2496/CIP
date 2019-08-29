<?php
namespace App\Repositories\Slider;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Slider;

interface SliderInterface
{
    /**
    * Get a count of slider.
    *
    * @return int
    */
    public function getAllSliderCount(): ?int;
    
    /**
     * Store tenant slider data.
     *
     * @param  array $data
     * @return App\Models\Slider
     */
    public function storeSlider(array $data): Slider;

    /**
     * Update tenant slider data.
     *
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateSlider(array $data, int $id): bool;

    /**
     * Get tenant sliders
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getSliders(): Collection;

    /**
     * Delete Slider
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool;
}
