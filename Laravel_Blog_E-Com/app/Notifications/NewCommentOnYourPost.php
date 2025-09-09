<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewCommentOnYourPost extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Comment $comment) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $post = $this->comment->post;
        $snippet = Str::limit(strip_tags($this->comment->body), 160);
        $url = route('posts.show', $post->slug).'#comments';

        return (new MailMessage)
            ->subject('New comment on: '.$post->title)
            ->greeting('Hi '.$notifiable->name.',')
            ->line('You have a new comment on your post.')
            ->line('"'.$snippet.'"')
            ->action('View the comment', $url);
    }
}
