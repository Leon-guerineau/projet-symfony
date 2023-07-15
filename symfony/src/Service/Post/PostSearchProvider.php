<?php

namespace App\Service\Post;

use App\Data\PostSearch;
use App\Repository\PostRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface as ServicePaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class PostSearchProvider
{
    const NB_RESULT_PER_PAGE = 25;
    const DEFAULT_PAGE = 1;

    public function __construct(
        private PostRepository            $postRepository,
        private ServicePaginatorInterface $paginator
    )
    {
    }

    public function getPostList(Request $request, ?PostSearch $postSearch): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->postRepository->findSearchQuery($postSearch ?? new PostSearch()),
            $request->query->getInt('page', self::DEFAULT_PAGE),
            self::NB_RESULT_PER_PAGE,
        );
    }
}