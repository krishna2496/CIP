<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;

class Language extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'language';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'language_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','code','status'];
    
    /**
     * Check language status.
     *
     * @param  int $id
     * @param  string $status
     * @return null|Collection
     */
    public function checkStatus(int $id, string $status): ?Collection
    {
        return $this->where(['language_id' => $id, 'status' => $status])->get();
    }
}
