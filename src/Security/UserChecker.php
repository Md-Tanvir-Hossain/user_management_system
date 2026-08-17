<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
   public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->getStatus() === 'blocked') {
            throw new CustomUserMessageAccountStatusException(
                'Your account has been blocked.'
            );
        }

        if ($user->getStatus() === 'unverified') {
            throw new CustomUserMessageAccountStatusException(
                'Your account has not been verified yet.'
            );
        }
    }

    public function checkPostAuth(
        UserInterface $user,
        ?TokenInterface $token = null
    ): void {
        // No additional post-authentication checks yet.
    }
}