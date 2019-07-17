<?php
namespace App\Transformations;

use App\User;

trait UserTransformable
{
    /**
     * Select user fields
     *
     * @param App\User $user
     * @return App\User
     */
    protected function transformUser(User $user): User
    {
        $prop = new User;
        $prop->user_id = (int) $user->user_id;
        $prop->first_name = $user->first_name;
        $prop->last_name = $user->last_name;
        $prop->email = $user->email;
        return $prop;
    }
}
