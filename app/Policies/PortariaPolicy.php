<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Portaria;

class PortariaPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Portaria $portaria) {
        return $user->id === $portaria->created_by;
    }
}
