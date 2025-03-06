<?php

namespace App\Services;

use App\Entity\User;

final class AnonymizerService
{
    private function anonymize(User $user): User
    {
        $user->setFirstName('');
        $user->setLastName('');

        return $user;
    }

    public function tryAnonymize(User $user): User
    {
        $user->setEmail('');

        if (!$user->hasAcceptedGdpr()) {
            return $user;
        }

        return $this->anonymize($user);
    }
}
