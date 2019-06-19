<?php
namespace App;

use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use App\Mission;

class MissionDocument extends Model
{
    use SoftDeletes;

    protected $table = 'mission_document';
    protected $primaryKey = 'mission_document_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

	protected $fillable = ['mission_id', 'document_name', 'document_type', 'document_path'];

    protected $visible = ['mission_document_id', 'document_name', 'document_type', 'document_path'];

	/**
     * Get the mission that has documents.
     */
	public function mission()
    {
    	return $this->belongsTo(Mission::class, 'mission_id', 'mission_id');
    }
}
