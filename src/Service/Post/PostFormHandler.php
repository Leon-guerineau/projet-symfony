<?php

namespace App\Service\Post;

use App\Data\PostSearch;
use App\Entity\Post;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class PostFormHandler
{
    public function __construct(private PostManager $postManager){}

    public function handleSearch(Request $request, FormInterface $form): ?PostSearch
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $form->getData();
        }
        return null;
    }

    public function handle(Request $request, FormInterface $form): ?Post
    {
        $form->handleRequest($request);
        $object = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->postManager->save($object);
        }
        return null;
    }
}
