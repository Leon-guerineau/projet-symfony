<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class HomeController extends AbstractController
{
    // Nombre d'éléments à afficher sur les listes
    const NB_ELEMENT_HOME = 10;

    // Recherche des 10 derniers éléments
    public function gethHomeList($arg)
    {
        return $arg->getRecent(self::NB_ELEMENT_HOME);
    }

    // Calculer Le nombre d'éléments à afficher sur le boutton "Voir Plus"
    public function getNbButton($arg, $nbElementHome = self::NB_ELEMENT_HOME): int
    {
        $nb = $arg
            ->createQueryBuilder('element')
            ->select('count(element.id)')
            ->getQuery()
            ->getSingleScalarResult();
        if ($nb - $nbElementHome < 0) {
            return 0;
        }
        return $nb - $nbElementHome;
    }

// Page d'Accueil

    #[Route('/', name: 'home')]
    public function home(UserRepository $userRepository, GameRepository $gameRepository, PostRepository $postRepository): Response
    {
        return $this->render('home/home.html.twig', [
            'users' => $this->gethHomeList($userRepository),
            'games' => $this->gethHomeList($gameRepository),
            'posts' => $this->gethHomeList($postRepository),
            'nbUsers' => $this->getNbButton($userRepository),
            'nbGames' => $this->getNbButton($gameRepository),
            'nbPosts' => $this->getNbButton($postRepository),
        ]);
    }
}
