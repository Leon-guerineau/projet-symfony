<?php

namespace App\Service\Post;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;

class PostManager
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Post $post): Post
    {
        $this->entityManager->persist($post);
        $this->entityManager->flush();
        return $post;
    }

    public function delete(Post $post)
    {
        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }
}

