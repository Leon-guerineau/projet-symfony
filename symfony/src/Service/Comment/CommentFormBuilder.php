<?php

namespace App\Service\Comment;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class CommentFormBuilder
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private CommentRepository    $commentRepository,
        private PostRepository       $postRepository,
    )
    {
    }

    public function getCreateForm(int $postId): FormInterface
    {
        $comment = new Comment();
        $comment->setPost($this->postRepository->find($postId));
        return $this->formFactory->create(CommentType::class, $comment);
    }

    public function getModifyForm(int $commentId): FormInterface
    {
        $comment = $this->commentRepository->find($commentId);
        return $this->formFactory->create(CommentType::class, $comment);
    }
}

