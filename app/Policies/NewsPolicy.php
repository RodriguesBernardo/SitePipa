<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, News $news)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->is_admin;
    }

    public function update(User $user, News $news)
    {
        return $user->is_admin || $user->id === $news->user_id;
    }

    public function delete(User $user, News $news)
    {
        return $user->is_admin;
    }
}