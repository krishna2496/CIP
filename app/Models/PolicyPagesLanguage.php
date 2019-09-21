<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\PolicyPage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyPagesLanguage extends Model
{
    use SoftDeletes;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'policy_pages_language';
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['page_id', 'language_id', 'title', 'description'];
    
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['language_id', 'title', 'description', 'sections'];
     
    /**
     * Set description attribute on the model.
     * @codeCoverageIgnore
     *
     * @param  array $value
     * @return void
     */
    public function setDescriptionAttribute(array $value): void
    {
        $this->attributes['description'] = serialize($value);
    }
    
    /**
     * Get an attribute from the model.
     * @codeCoverageIgnore
     * @param  string $value
     * @return array
     */
    public function getDescriptionAttribute(string $value): array
    {
        return unserialize($value);
    }

    /**
     * Get an attribute from the model.
     * @codeCoverageIgnore
     * @param  string $value
     * @return array
     */
    public function getSectionsAttribute(string $value): array
    {
        return unserialize($value);
    }

    /**
     * Store/update specified resource.
     * @codeCoverageIgnore
     * @param  array $condition
     * @param  array $data
     * @return App\Models\PolicyPagesLanguage
     */
    public function createOrUpdatePolicyPagesLanguage(array $condition, array $data): PolicyPagesLanguage
    {
        return static::updateOrCreate($condition, $data);
    }
}
