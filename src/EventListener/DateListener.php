<?php

namespace App\EventListener;

use App\Repository\AdminUserRepository;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Events;
use App\Entity\Advert;
use App\Entity\Picture;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;

#[AsDoctrineListener(Events::prePersist)]
#[AsDoctrineListener(Events::preUpdate)]
class DateListener
{
    private MailerInterface $mailer;
    private AdminUserRepository $adminUserRepository;

    public function __construct(MailerInterface $mailer,AdminUserRepository $adminUserRepository)
    {
        $this->mailer = $mailer;
        $this->adminUserRepository = $adminUserRepository;;
    }

    public function prePersist(EventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();

        if ($entity instanceof Advert || $entity instanceof Picture) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }
        if ($entity instanceof Advert) {
            $adminList = $this->adminUserRepository->findAll();
            foreach ($adminList as $admin) {
                $this->mailer->send((new NotificationEmail())
                    ->subject('Nouvelle annonce créé')
                    ->from('admin@lebonangle.fr')
                    ->to($admin->getEmail())
                );
            }
        }
    }

    public function preUpdate(EventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();

        if ($entity instanceof Advert) {
            if ($entity->getState() === "published") {
                $this->mailer->send((new NotificationEmail())
                    ->subject('Nouvelle annonce publié')
                    ->htmlTemplate('emails/admin.html.twig')
                    ->from('admin@lebonangle.fr')
                    ->to($entity->getEmail())
                    ->context([
                        'advert' => $entity,
                    ])
                );
                $entity->setPublishedAt(new \DateTimeImmutable());
            }
        }
    }
}
