<?php
namespace App\Repositories\Story;

use Illuminate\Http\Request;
use App\Models\Story;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface StoryInterface
{
	/**
	 * Fetch Story details
	 *
	 * @param int $timesheetId
	 * @return null|Timesheet
	 */
	public function find(int $timesheetId): ?Story;
	
	/**
	 * Remove the Story Data.
	 *
	 * @param  int  $id
	 * @return bool
	 */
	public function delete(int $id): bool;
	
	/**
	 * Display a user story listing.
	 *
	 * @param Illuminate\Http\Request $request
	 * @param int $userId
	 * @return \Illuminate\Pagination\LengthAwarePaginator
	 */
	public function getUserStories(Request $request, int $userId): LengthAwarePaginator;
	
}