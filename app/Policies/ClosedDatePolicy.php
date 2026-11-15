<?php

namespace App\Policies;

use App\Models\ClosedDate;
use App\Models\User;

class ClosedDatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ClosedDate $closedDate): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ClosedDate $closedDate): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ClosedDate $closedDate): bool
    {
        return $user->isAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
