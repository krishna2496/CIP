<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordInterface;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\{City, Country, Timezone};

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordInterface
{
    use Authenticatable, Authorizable;
    use CanResetPasswordTrait;
    use Notifiable;
    use SoftDeletes;

    protected $table = "user";
    protected $primaryKey = "user_id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'password', 'avatar', 'timezone_id', 'availability_id', 'why_i_volunteer', 
							'employee_id', 'department', 'manager_name', 'city_id', 'country_id', 'profile_text', 'linked_in_url', 'status'];
	
	protected $visible = ['user_id', 'first_name', 'last_name', 'email', 'password', 'avatar', 'timezone_id', 'availability_id', 'why_i_volunteer', 
							'employee_id', 'department', 'manager_name', 'city_id', 'country_id', 'profile_text', 'linked_in_url', 'status', 'city', 'country', 'timezone'];
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];
	
	public $rules = [
        // Validation rules
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
	
	public $loginRules = [
        // Validation rules
		'email' => 'required|email',
		'password' => 'required'
    ];
	
	public $resetPasswordRules = [
		'email' => 'required|email',
	];
	
	/*
    * Defined has many relation for the city table.
    */
    public function city()
    {
    	return $this->hasOne(City::class, 'city_id', 'city_id');
    }
	
	/*
    * Defined has many relation for the country table.
    */
    public function country()
    {
    	return $this->hasOne(Country::class, 'country_id', 'country_id');
    }
	
	/*
    * Defined has many relation for the country table.
    */
    public function timezone()
    {
    	return $this->hasOne(Timezone::class, 'timezone_id', 'timezone_id');
    }
	
    /**
     * The is set attribute method for password. This will make has of entered password, before insert.
     *
     * @param string $password
     * @return void
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);        
    }
	
	public function findUser(int $id)
    {
        return static::with('city', 'country', 'timezone')->findOrFail($id);
    }
	
	public function deleteUser(int $id)
    {
        return static::findOrFail($id)->delete();
    }
}
