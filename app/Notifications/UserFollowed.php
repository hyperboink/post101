<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\User;

class UserFollowed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $userFollow;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $userFollow)
    {
        $this->userFollow = $userFollow;
    }

    /**
     * Get the notification's database channels.
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
            'action' => 'follower',
            'id' => $this->userFollow->id,
            'name' => $this->userFollow->name  
        ];
    }
}
