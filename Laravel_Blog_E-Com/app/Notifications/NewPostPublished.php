<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewPostPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Post $post) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $excerpt = Str::limit(strip_tags($this->post->excerpt ?? $this->post->body), 140);
        $url     = route('posts.show', $this->post->slug);

        return (new MailMessage)
            ->subject('New post: '.$this->post->title)
            ->greeting('Hi '.$notifiable->name.',')
            ->line('A new post was published in '.($this->post->category?->name ?? 'our blog').'.')
            ->line('「 '.$this->post->title.' 」')
            ->line($excerpt)
            ->action('Read the post', $url)
            ->line('— '.$this->post->author?->name);
    }
}
