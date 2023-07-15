<?php

namespace App\EventSubscriber;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class GameSubscriber implements EventSubscriberInterface
{

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function setTimestampable(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Game) {
            return;
        }

        if (!$entity->getId()) { //création
            $entity->setCreatedAt(date_create_immutable());
        }

        $entity->setUpdatedAt(date_create_immutable());
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->setTimestampable($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->setTimestampable($args);
    }
}