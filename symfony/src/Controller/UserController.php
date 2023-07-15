<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    const NB_ELEMENT_LIST = 25;

    #[Route('/utilisateurs', name: 'user_list')]
    public function users(UserRepository $userRepository): Response
    {
        return $this->render('user/user-list.html.twig',[
            'users' => $userRepository->getRecent(self::NB_ELEMENT_LIST),
            'nbUsers' => $userRepository->getNb(),
        ]);
    }
}
