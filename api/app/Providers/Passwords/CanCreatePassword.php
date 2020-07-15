<?php

namespace App\Providers\Passwords;

use App\Notifications\CreatePassword;

trait CanCreatePassword
{
    /**
     * Get the e-mail address where password create links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordCreate()
    {
        return $this->email;
    }

    /**
     * Send the password create notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordCreateNotification($token)
    {
        $this->notify(new CreatePassword($token));
    }
}
