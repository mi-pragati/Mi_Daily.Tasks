<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;

class CommentPolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'editor';
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->role === 'editor' && $user->id === $comment->user_id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id || $user->role === 'admin';
    }
}
