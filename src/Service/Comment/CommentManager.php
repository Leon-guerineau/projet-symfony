<?php

namespace App\Service\Comment;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;

class CommentManager
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(Comment $comment): Comment
    {
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
        return $comment;
    }

    public function delete(Comment $comment)
    {
        $this->entityManager->remove($comment);
        $this->entityManager->flush();
    }
}

