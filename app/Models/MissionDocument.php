<?php
namespace App\Models;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use App\Mission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissionDocument extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mission_document';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'mission_document_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mission_id', 'document_name', 'document_type', 'document_path'];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['mission_document_id', 'document_name', 'document_type', 'document_path'];

    /**
     * Get the mission that has documents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class, 'mission_id', 'mission_id');
    }

    /**
     * Store/update specified resource.
     *
     * @param  array $condition
     * @param  array $data
     * @return array
     */
    public function createOrUpdateDocument(array $condition, array $data)
    {
        return static::updateOrCreate($condition, $data);
    }
}
