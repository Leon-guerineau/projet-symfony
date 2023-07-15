<?php

namespace App\Service\Post;

use App\Data\PostSearch;
use App\Entity\Post;
use App\Form\PostSearchType;
use App\Form\PostType;
use App\Repository\GameRepository;
use App\Repository\PostRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class PostFormBuilder
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private PostRepository $postRepository,
        private GameRepository $gameRepository,
    )
    {
    }

    public function getSearchForm(): FormInterface
    {
        $data = new PostSearch();
        return $this->formFactory->create(PostSearchType::class, $data);
    }

    public function getCreateForm(int $gameId): FormInterface
    {
        $post = new Post();
        $post->setGame($this->gameRepository->find($gameId));
        return $this->formFactory->create(PostType::class, $post, ['validation_groups' => ['create']]);
    }

    public function getModifyForm(int $postId): FormInterface
    {
        $post = $this->postRepository->find($postId);
        return $this->formFactory->create(PostType::class, $post, ['validation_groups' => ['modify']]);
    }
}

