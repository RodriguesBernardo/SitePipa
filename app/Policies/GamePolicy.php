<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GamePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Game $game)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->is_admin;
    }

    public function update(User $user, Game $game)
    {
        return $user->is_admin;
    }

    public function delete(User $user, Game $game)
    {
        return $user->is_admin;
    }

    public function restore(User $user, Game $game)
    {
        return $user->is_admin;
    }

    public function forceDelete(User $user, Game $game)
    {
        return $user->is_admin;
    }
}