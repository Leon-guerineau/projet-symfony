<?php

namespace App\EventSubscriber;

use App\Entity\Game;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class PictureSubscriber implements EventSubscriberInterface
{
    public function __construct(private SluggerInterface $slugger)
    {

    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preRemove,
        ];
    }

    /**
     * Processes uploaded file before it's element is persisted in the database
     */
    public function prePersist(LifecycleEventArgs $arg): void
    {
        // Gets the Entity
        $entity = $arg->getObject();
        // Processes Entity depending on it's Class
        if ($entity instanceof Post || $entity instanceof Game || $entity instanceof User){
            // Processes Entity's picture if it doesn't already have one
            if ($entity->getPicturePath() === null){
                // Gets File & Path
                $postFile = $entity->getPictureDwlFile();
                $picturePath = get_class($entity)::PICTURE_PATH;
                // Creates the File's folder if it doesn't exist
                if (!file_exists($picturePath) && !is_dir($picturePath)) {
                    mkdir($picturePath);
                }
                // Renames and Moves the File
                $newFileName = $this->slugger->slug(uniqid() . ' ' . $postFile->guessExtension(), '.');
                $postFile->move($picturePath, $newFileName);
                // Sets Entity's picturePath
                $entity->setPicturePath($picturePath . $newFileName);
            }
        }
    }

    /**
     * Removes file before it's element is removed
     */
    public function preRemove(LifecycleEventArgs $arg): void
    {
        $entity = $arg->getObject();
        if ($entity instanceof Post || $entity instanceof Game || $entity instanceof User){
            if(file_exists($entity->getPicturePath())) {
            unlink($entity->getPicturePath());
            }
        }
    }
}
