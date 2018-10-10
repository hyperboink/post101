<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Post;
use App\User;

class NewPost extends Notification implements ShouldQueue
{
    use Queueable;

    protected $post;
    protected $user;

    public function __construct(Post $post, User $user)
    {
        $this->post = $post;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'action' => 'poster',
            'id' => $this->user->id,
            'name' => $this->user->name,
            'post_id' => $this->post->id
        ];
    }

}
