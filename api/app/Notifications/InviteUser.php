<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class InviteUser extends Notification
{
    /**
     * The mail variables.
     *
     * @var array
     */
    public $mailConfig;

    /**
     * Create a notification instance.
     *
     * @param  array  $mailConfig
     * @return void
     */
    public function __construct($mailConfig)
    {
        $this->mailConfig = $mailConfig;
    }

    /**
     * Get the notification's channels.
     *
     * @return array|string
     */
    public function via()
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view('vendor.notifications.invite', $this->mailConfig)
            ->subject($this->mailConfig['subject']);
    }
}
