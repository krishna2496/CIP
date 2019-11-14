<?php
namespace App\Repositories\MigrationChanges;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\MigrationSeederFiles;

interface MigrationChangesInterface
{
    /**
     * Store file details in database
     *
     * @param string $fileName
     * @param string $type
     * @return App\Models\MigrationSeederFiles
     */
    public function storeDetails(string $fileName, string $fileType): MigrationSeederFiles;
}
