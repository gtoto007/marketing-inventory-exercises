<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Only managers or admins can see the cost of an event.
     */
    public function viewCost(User $user, Event $event): bool
    {
        return $user->role === UserRole::MANAGER || $user->role === UserRole::ADMIN;
    }

    /**
     * Only the administration team can see item availability, with or without an event.
     */
    public function viewItemAvailability(User $user, ?Event $event = null): bool
    {
        return $user->role === UserRole::ADMIN;
    }
}
