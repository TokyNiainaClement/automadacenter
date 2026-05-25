<?php

namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserListener
{
    private UserPasswordHasherInterface $haser;

    public function __construct(UserPasswordHasherInterface $haser)
    {
        $this->haser = $haser;
    }

    public function prePersist(User $user)
    {
        $this->encodePassword($user);
    }

    public function preUpdate(User $user)
    {
        $this->encodePassword($user);
    }

    private function encodePassword(User $user)
    {
        if ($user->getPlaintextPassword() == null) {
            return;
        }

        $user->setPassword(
            $this->haser->hashPassword($user, $user->getPlaintextPassword())
        );
    }
}
