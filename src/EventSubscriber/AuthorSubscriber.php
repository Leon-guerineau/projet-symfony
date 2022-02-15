<?php

namespace App\EventSubscriber;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthorSubscriber implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Post and $entity->getAuthor() === null) {
            $entity->setAuthor($this->tokenStorage->getToken()->getUser()->getUserObject());
        }
        if ($entity instanceof Comment and $entity->getAuthor() === null) {
            $entity->setAuthor($this->tokenStorage->getToken()->getUser()->getUserObject());
        }
    }
}