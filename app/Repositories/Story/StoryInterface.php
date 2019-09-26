<?php
namespace App\Repositories\Story;

use Illuminate\Http\Request;
use App\Models\Story;

interface StoryInterface
{
    /**
     * Store story details
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\Story
     */
    public function store(Request $request): Story;
}
