<?php

namespace App\Event\Subscriber\Security;

use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: User::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: User::class)]
class PasswordHasherSubscriber
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function prePersist(User $user): void
    {
        $this->hashPassword($user);
    }

    public function preUpdate(User $user): void
    {
        if ($this->isPasswordModified($user)) {
            $this->hashPassword($user);
        }
    }

    public function hashPassword(User $user): void
    {
        $plainPassword = $user->getPlainPassword();
        if ($plainPassword) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword), true);
            $user->eraseCredentials();
        }
    }

    public function isPasswordModified(User $user): bool
    {
        return null !== $user->getPlainPassword() && null === $user->getPassword();
    }
}
