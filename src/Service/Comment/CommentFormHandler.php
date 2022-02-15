<?php

namespace App\Service\Comment;

use App\Entity\Comment;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CommentFormHandler
{
    public function __construct(private CommentManager $commentManager){}

    public function handle(Request $request, FormInterface $form): ?Comment
    {
        $form->handleRequest($request);
        $object = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->commentManager->save($object);
        }
        return null;
    }
}
