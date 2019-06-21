<?php
namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordInterface;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use App\Models\{City, Country, Timezone};

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordInterface
{
    use Authenticatable, Authorizable, CanResetPasswordTrait, Notifiable, SoftDeletes;

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
    protected $fillable = ['first_name', 'last_name', 'email', 'password', 'avatar', 'timezone_id', 'availability_id', 'why_i_volunteer', 'employee_id', 'department', 'manager_name', 'city_id', 'country_id', 'profile_text', 'linked_in_url', 'status', 'language_id'];
    
    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['user_id', 'first_name', 'last_name', 'email', 'password', 'avatar', 'timezone_id', 'availability_id', 'why_i_volunteer', 'employee_id', 'department', 'manager_name', 'city_id', 'country_id', 'profile_text', 'linked_in_url', 'status', 'city', 'country', 'timezone'];
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
    
    /**
     * The rules that should validate create request.
     *
     * @var array
     */
    public $rules = [
        "first_name" => "required|max:16",
        "last_name" => "required|max:16",
        "email" => "required|email|unique:user,email,NULL,user_id,deleted_at,NULL",
        "password" => "required",
        "city_id" => "required",
        "country_id" => "required",
        "profile_text" => "required",
        "employee_id" => "max:16",
        "department" => "max:16",
        "manager_name" => "max:16",
        "linked_in_url" => "url"
    ];
    
    /**
     * The rules that should validate login request.
     *
     * @var array
     */
    public $loginRules = [
        'email' => 'required|email',
        'password' => 'required'
    ];
    
    /**
     * The rules that should validate reset password request.
     *
     * @var array
     */
    public $resetPasswordRules = [
        'email' => 'required|email',
    ];
        
    /**
    * Defined has one relation for the city table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    */
    public function city()
    {
        return $this->hasOne(City::class, 'city_id', 'city_id');
    }
    
    /**
    * Defined has one relation for the country table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    */
    public function country()
    {
        return $this->hasOne(Country::class, 'country_id', 'country_id');
    }
    
    /**
    * Defined has one relation for the timezone table.
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasOne
    */
    public function timezone()
    {
        return $this->hasOne(Timezone::class, 'timezone_id', 'timezone_id');
    }
    
    /**
     * Defined has one relation for the user_skill table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userSkills()
    {
        return $this->hasMany(UserSkill::class, 'user_id', 'user_id');
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
     * @return array
     */
    public function deleteUser(int $id)
    {
        return static::findOrFail($id)->delete();
    }
}
