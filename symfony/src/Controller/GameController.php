<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game/', name: 'game_')]
class GameController extends AbstractController
{
    const NB_ELEMENT_LIST = 25;

    public function __construct(
        private GameRepository $gameRepository,
        private PostRepository $postRepository,
    )
    {
    }

    /**
     * Game List
     */
    #[Route('/list', name: 'list')]
    public function games(): Response
    {
        return $this->render('game/game-list.html.twig', [
            'games' => $this->gameRepository->getRecent(self::NB_ELEMENT_LIST),
            'nbGames' => $this->gameRepository->getNb(),
        ]);
    }

    /**
     * Game Profile Page
     */
    #[Route('show/{gameId}', name: 'show')]
    public function showPost(int $gameId): Response
    {
        // Render
        return $this->render('game/game-show.html.twig', [
            'game' => $this->gameRepository->find($gameId),
            'posts' => $this->postRepository->getByGameId($gameId),
        ]);
    }
}
