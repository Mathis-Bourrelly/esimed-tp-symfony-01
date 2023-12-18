<?php

namespace App\EventListener;
use App\Entity\AdminUser;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsDoctrineListener(Events::prePersist)]
#[AsDoctrineListener(Events::preUpdate)]
class HasherListener
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function prePersist(EventArgs $eventArgs): void
    {
        $this->hashPassword($eventArgs);
    }

    public function preUpdate(EventArgs $eventArgs): void
    {
        $this->hashPassword($eventArgs);
    }

    public function hashPassword(EventArgs $eventArgs): void
    {
        $user = $eventArgs->getObject();
        if (!$user instanceof AdminUser){
            return;
        }
        $plainPassword = $user->getPlainPassword();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
    }
}
