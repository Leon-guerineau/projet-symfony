<?php

namespace App\EventSubscriber;

use App\Entity\Post;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;

class DeleteSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove,
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();
        if ($entity instanceof Post){
            $comments = $entity->getComments();
            foreach ($comments as $comment){
                $entityManager->remove($comment);
                $entityManager->flush();
            }
        }
    }
}
