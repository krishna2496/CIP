<?php
namespace App\Repositories\News;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface NewsInterface
{
    /**
     * Store news.
     *
     * @param \Illuminate\Http\Request $request
     * @return App\Models\News
     */
    public function store(Request $request): News;

    /**
     * Update news.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $newsId
     * @return App\Models\News
     */
    public function update(Request $request, int $newsId): News;

      /**
     * Get news details.
     *
     * @param int $id
     * @param int $languageId
     * @param string $newsStatus
     * @return App\Models\News
     */
    public function getNewsDetails(int $id, int $languageId, string $newsStatus): News;

    /**
     * Remove news.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Display news lists.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $languageId
     * @param string $newsStatus
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getNewsList(Request $request, int $languageId, string $newsStatus = null): LengthAwarePaginator;
    
    /**
     * Display news lists admin.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getNewsListAdmin(Request $request): LengthAwarePaginator;

    /**
     * Get news details admin.
     *
     * @param int $id
     * @return App\Models\News
     */
    public function getNewsDetailsAdmin(int $id): News;
}
