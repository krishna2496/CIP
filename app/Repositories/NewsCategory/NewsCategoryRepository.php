<?php
namespace App\Repositories\NewsCategory;

use App\Repositories\NewsCategory\NewsCategoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\NewsCategory;
use Illuminate\Support\Collection;
use \Illuminate\Pagination\LengthAwarePaginator;

class NewsCategoryRepository implements NewsCategoryInterface
{
    /**
     * @var App\Models\NewsCategory
     */
    private $newsCategory;
 
    /**
     * Create a new NewsCategory repository instance.
     *
     * @param  App\Models\NewsCategory $newsCategory
     * @return void
     */
    public function __construct(NewsCategory $newsCategory)
    {
        $this->newsCategory = $newsCategory;
    }
   
    /**
     * Display news category details.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getNewsCategoryDetails(Request $request): LengthAwarePaginator
    {
        return $this->newsCategory->paginate($request->perPage);
    }

    /**
     * Store news category.
     *
     * @param array $request
     * @return App\Models\NewsCategory
     */
    public function store(array $request): NewsCategory
    {
        return $this->newsCategory->create($request);
    }

    /**
     * Update news category.
     *
     * @param  array  $request
     * @param  int  $id
     * @return App\Models\NewsCategory
     */
    public function update(array $request, int $id): NewsCategory
    {
        $newsCategory = $this->newsCategory->findOrFail($id);
        $newsCategory->update($request);
        return $newsCategory;
    }
    
    /**
     * Find news category.
     *
     * @param  int  $id
     * @return App\Models\NewsCategory
     */
    public function find(int $id): NewsCategory
    {
        return $this->newsCategory->findNewsCategory($id);
    }
    
    /**
     * Remove news category.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->newsCategory->deleteNewsCategory($id);
    }
}
