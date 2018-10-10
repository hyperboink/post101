<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\User;
use App\Like;

class LikedComment extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $like;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Like $like)
    {
        $this->user = $user;
        $this->like = $like;
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
    public function toDtabase($notifiable)
    {
        return [
            'action' => 'liker',
            'id' => $this->user->id,
            'name' => $this->user->name,
            'post_id' => $this->like->post_id
        ];
    }
}
