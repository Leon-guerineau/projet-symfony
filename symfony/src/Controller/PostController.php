<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\GameRepository;
use App\Repository\PostRepository;
use App\Service\Post\PostFormBuilder;
use App\Service\Post\PostFormHandler;
use App\Service\Post\PostManager;
use App\Service\Post\PostSearchProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/post/', name: 'post_')]
class PostController extends AbstractController
{
    public function __construct(
        private PostFormHandler       $postFormHandler,
        private PostSearchProvider    $postSearchProvider,
        private PostFormBuilder       $postFormBuilder,
        private PostManager           $postManager,
        private PostRepository        $postRepository,
        private GameRepository        $gameRepository,
        private CommentRepository     $commentRepository,
        private TokenStorageInterface $tokenStorage,
    )
    {
    }

    public function doGameExist($gameId): bool
    {
        if (is_numeric($gameId)) {
            $game = $this->gameRepository->findOneBy(array('id' => $gameId));
            if ($game !== null) {
                return true;
            }
        }
        $this->addFlash('danger', "Ce Jeu n'existe pas !");
        return false;
    }

    public function doPostExist($postId): bool
    {
        if (is_numeric($postId)) {
            $post = $this->postRepository->findOneBy(array('id' => $postId));
            if ($post !== null) {
                return true;
            }
        }
        $this->addFlash('danger', "Ce Post n'existe pas !");
        return false;
    }

    public function isAuthorOfPost($postId): bool
    {
        if ($this->doPostExist($postId)) {
            $post = $this->postRepository->findOneBy(array('id' => $postId));
            if ($this->isGranted('ROLE_USER') && $this->tokenStorage->getToken()->getUser() === $post->getAuthor()) {
                return true;
            }
        }
        $this->addFlash('danger', "Vous n'êtes pas l'auteur de ce Post !");
        return false;
    }

    public function isAdmin(): bool
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return true;
        }
        $this->addFlash('danger', "Vous n'avez pas l'authorisation !");
        return false;
    }

    /**
     * Post list
     */
    #[Route('list', name: 'list')]
    public function listPost(Request $request): Response
    {
        // Search Form
        $form = $this->postFormBuilder->getSearchForm();
        $search = $this->postFormHandler->handleSearch($request, $form);
        $posts = $this->postSearchProvider->getPostList($request, $search);
        // Render
        return $this->render('post/post-list.html.twig', [
            'posts' => $posts,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Post Creation
     */
    #[Route('create/{gameId}', name: 'create')]
    public function createPost($gameId, Request $request): Response
    {
        // Security
        if (!$this->doGameExist($gameId)) {
            return $this->redirectToRoute('home');
        }
        // Form and Post
        $form = $this->postFormBuilder->getCreateForm($gameId);
        $post = $this->postFormHandler->handle($request, $form);
        // Valid Form Redirect
        if ($post !== null) {
            $this->addFlash('success', 'Votre Post à bien été crée !');
            return $this->redirectToRoute('post_show', ['postId' => $post->getId()]);
        }
        // Invalid Form Flash
        if ($form->isSubmitted()) {
            $this->addFlash('warning', 'Le formulaire est invalide !');
        }
        // Render
        return $this->render('post/post-form.html.twig', [
            'form' => $form->createView(),
            'gameId' => $gameId,
        ]);
    }

    /**
     * Post Profile
     */
    #[Route('show/{postId}', name: 'show')]
    public function showPost($postId): Response
    {
        // Security
        if (!$this->doPostExist($postId)) {
            return $this->redirectToRoute('home');
        }
        // Render
        return $this->render('post/post-show.html.twig', [
            'post' => $this->postRepository->findOneBy(array('id' => $postId)),
            'comments' => $this->commentRepository->getByPostId($postId),
        ]);
    }

    /**
     * Post Modification
     */
    #[Route('modify/{postId}', name: 'modify')]
    public function modifyPost($postId, Request $request): Response
    {
        // Security
        if (!$this->doPostExist($postId) || !$this->isAuthorOfPost($postId)) {
            return $this->redirectToRoute('home');
        }
        // Form & Post
        $form = $this->postFormBuilder->getModifyForm($postId);
        $post = $this->postFormHandler->handle($request, $form);
        // Modification Redirect
        if ($post !== null) {
            $this->addFlash('success', 'Le Post à bien été modifié !');
            return $this->redirectToRoute('post_show', ['postId' => $postId]);
        }
        // Render
        return $this->render('post/post-form.html.twig', [
            'form' => $form->createView(),
            'postId' => $postId,
        ]);
    }

    /**
     * Post Removal
     */
    #[Route('delete/{postId}', name: 'delete')]
    public function deletePost($postId): Response
    {
        // Security
        if (!$this->doPostExist($postId) || !$this->isAuthorOfPost($postId) && !$this->isAdmin()) {
            return $this->redirectToRoute('home');
        }
        // GameId for Redirection after removal
        $post = $this->postRepository->find($postId);
        $gameId = $post->getGame()->getId();
        // Delete Post
        $this->postManager->delete($post);
        $this->addFlash('success', 'Le Post à bien été supprimé !');
        // Redirect to Game
        return $this->redirectToRoute('game_show', ['gameId' => $gameId]);
    }

    /**
     * Cancel Post Creation
     */
    #[Route('create/{gameId}/cancel', name: 'create_cancel')]
    public function cancelPostCreation($gameId): Response
    {
        // Security
        if (!$this->doGameExist($gameId)) {
            return $this->redirectToRoute('home');
        }
        // Redirect to Game
        $this->addFlash('secondary', 'Création du Post annulée !');
        return $this->redirectToRoute('game_show', ['gameId' => $gameId]);
    }

    /**
     * Cancel Post Modification
     */
    #[Route('modify/{postId}/cancel', name: 'modify_cancel')]
    public function cancelPostModification($postId): Response
    {
        // Security
        if (!$this->doPostExist($postId) || !$this->isAuthorOfPost($postId)) {
            return $this->redirectToRoute('home');
        }
        // Redirect to Post
        $this->addFlash('secondary', 'Modification du Post annulée !');
        return $this->redirectToRoute('post_show', ['postId' => $postId]);
    }

    /**
     * Cancel Post Removal
     */
    #[Route('delete/{postId}/cancel', name: 'delete_cancel')]
    public function cancelPostRemoval($postId): Response
    {
        // Security
        if (!$this->doPostExist($postId) || !$this->isAuthorOfPost($postId) && !$this->isAdmin()) {
            return $this->redirectToRoute('home');
        }
        // Redirect to Post
        $this->addFlash('secondary', 'Suppression du Post annulée !');
        return $this->redirectToRoute('post_show', ['postId' => $postId]);
    }
}
