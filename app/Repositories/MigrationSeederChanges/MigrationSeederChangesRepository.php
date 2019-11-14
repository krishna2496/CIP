<?php
namespace App\Repositories\MigrationSeederChanges;

use App\Repositories\MigrationSeederChanges\MigrationSeederChangesInterface;
use Illuminate\Http\Request;
use App\Models\MigrationSeederFiles;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use DB;

class MigrationSeederChangesRepository implements MigrationSeederChangesInterface
{
    /**
     * @var App\Models\MigrationSeederFiles
     */
    public $migrationSeederFiles;

    /**
     * Create a new Tenant has setting repository instance.
     *
     * @param  App\Models\MigrationSeederFiles $migrationSeederFiles
     * @return void
     */
    public function __construct(MigrationSeederFiles $migrationSeederFiles)
    {
        $this->migrationSeederFiles = $migrationSeederFiles;
    }

    /**
     * Store file details in database
     *
     * @param string $fileName
     * @param string $type
     * @return App\Models\MigrationSeederFiles
     */
    public function storeDetails(string $fileName, string $fileType): MigrationSeederFiles
    {
        return $this->migrationSeederFiles
        ->create([
            'file_name' => $fileName,
            'type' => $fileType
        ]);
    }
}
