<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['owner', 'admin', 'cashier'], true);
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return $user->isOwner();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isOwner();
    }

    public function forceDelete(User $user, Reservation $reservation): bool
    {
        return $user->isOwner();
    }
}
