<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Mission;

class NewMissionCreated extends Notification
{
    /**
     * @var App\Models\Mission
     */
    protected $mission;

    /**
     * @var App\User
     */
    protected $user;

    /**
     * @var string
     */
    protected $action;
    
    /**
     * Create a new notification instance.
     *
     * @param App\Models\Mission $mission
     * @param App\User $user
     * @param string $action
     *
     * @return void
     */
    public function __construct($mission, $user, $action)
    {
        $this->mission = $mission;
        $this->user = $user;
        $this->action = $action;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'notification_type_id' => 1,
            'user_id' => $this->user,
            'is_read' => 0,
            'entity_id' => $this->mission,
            'action' => $this->action
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
