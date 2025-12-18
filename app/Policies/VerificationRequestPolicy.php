<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class VerificationRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isAdmin(); // Assuming you have isAdmin() method
    }

    public function view(User $user, VerificationRequest $verificationRequest)
    {
        return $user->isAdmin() || $user->id === $verificationRequest->user_id;
    }

    public function create(User $user)
    {
        return !$user->verificationRequest()->exists();
    }

    public function update(User $user, VerificationRequest $verificationRequest)
    {
        return $user->isAdmin() && $verificationRequest->isPending();
    }

    public function delete(User $user, VerificationRequest $verificationRequest)
    {
        return $user->isAdmin();
    }
}