<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AppInformation extends Notification
{
    use Queueable;

    public $notificacionInfo;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notificacionInfo)
    {
        $this->notificacionInfo = $notificacionInfo;
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
    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->notificacionInfo['user_id'],
            'title' => $this->notificacionInfo['title'],
            'body' => $this->notificacionInfo['body'],
            'media' => isset($this->notificacionInfo['media']) ? $this->notificacionInfo['media'] : '',
            'mediaType' => isset($this->notificacionInfo['mediaType']) ? $this->notificacionInfo['mediaType'] : '',
        ];
    }
}
