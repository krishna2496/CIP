<?php
namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordInterface;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use App\Models\City;
use App\Models\Country;
use App\Models\Timezone;
use App\Models\missionApplication;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Nicolaslopezj\Searchable\SearchableTrait;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordInterface
{
    use Authenticatable, Authorizable, CanResetPasswordTrait, Notifiable, SoftDeletes, SearchableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "user";

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = "user_id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'password', 'avatar',
     'timezone_id', 'availability_id', 'why_i_volunteer', 'employee_id', 'department',
      'manager_name', 'city_id', 'country_id', 'profile_text', 'linked_in_url', 'status',
       'language_id'];
    
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['user_id', 'first_name', 'last_name', 'email',
     'password', 'avatar', 'timezone_id', 'availability_id', 'why_i_volunteer',
     'employee_id', 'department', 'manager_name', 'city_id', 'country_id',
     'profile_text', 'linked_in_url', 'status', 'city', 'country', 'timezone', 'language_id'];
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
  
    /**
     * Searchable rules.
     *
     * @var array
     */
    protected $searchable = [
        'columns' => [
            'user.first_name' => 10,
            'user.last_name' => 10,
            'user.email' => 10
        ]
    ];

    /**
    * Defined has one relation for the city table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    */
    public function city(): HasOne
    {
        return $this->hasOne(City::class, 'city_id', 'city_id');
    }
    
    /**
    * Defined has one relation for the country table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    */
    public function country(): HasOne
    {
        return $this->hasOne(Country::class, 'country_id', 'country_id');
    }
    
    /**
    * Defined has one relation for the timezone table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    */
    public function timezone(): HasOne
    {
        return $this->hasOne(Timezone::class, 'timezone_id', 'timezone_id');
    }
    
    /**
     * Defined has many relation for the user_skill table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userSkills(): HasMany
    {
        return $this->hasMany(UserSkill::class, 'user_id', 'user_id');
    }

    /**
     * Defined has many relation for the mission_application table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function missionApplication(): HasMany
    {
        return $this->hasMany(MissionApplication::class, 'user_id', 'user_id');
    }

    /**
     * The is set attribute method for password. This will make has of entered password, before insert.
     *
     * @param string $password
     * @return void
     */
    public function setPasswordAttribute(string $password)
    {
        $this->attributes['password'] = Hash::make($password);
    }
    
    /**
     * Find the specified resource.
     *
     * @param  int  $id
     * @return array
     */
    public function findUser(int $id)
    {
        return static::with('city', 'country', 'timezone')->findOrFail($id);
    }
    
    /**
     * Delete the specified resource.
     *
     * @param  int  $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        return static::findOrFail($id)->delete();
    }

    /**
     * Get specified resource.
     *
     * @param int $missionId
     * @return string
     */
    public function getUserName(int $userId): string
    {
        return static::select('first_name')->where(['user_id' => $userId])->value('first_name');
    }

    /**
     * Search user
     *
     * @param string $term
     * @param int $userId
     *
     * @return mixed
     */
    public function searchUser($term, $userId)
    {
        return self::where('user_id', '<>', $userId)->search($term);
    }

    /**
     * Search user
     *
     * @param string $email     *
     * @return mixed
     */
    public function getUserByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }
}
