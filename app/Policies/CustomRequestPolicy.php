<?php 

namespace App\Policies;

use App\Models\CustomRequest;
use App\Models\User;

class CustomRequestPolicy
{
    public function viewAny(User $user)
    {
        return $user !== null;
    }

    public function view(User $user, CustomRequest $customRequest)
    {
        return $user->id === $customRequest->user_id;
    }

    public function create(User $user)
    {
        return $user !== null;
    }
}