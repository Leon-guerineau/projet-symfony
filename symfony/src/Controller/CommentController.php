<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Service\Comment\CommentFormBuilder;
use App\Service\Comment\CommentFormHandler;
use App\Service\Comment\CommentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/comment/', name: 'comment_')]
class CommentController extends AbstractController
{
    public function __construct(
        private CommentFormBuilder    $commentFormBuilder,
        private CommentFormHandler    $commentFormHandler,
        private CommentManager        $commentManager,
        private CommentRepository     $commentRepository,
        private PostRepository        $postRepository,
        private TokenStorageInterface $tokenStorage,
    )
    {
    }

    public function getPostId($commentId): int
    {
        return $this->commentRepository->find($commentId)->getPost()->getId();
    }

    public function doPostExist($postId): bool
    {
        if (is_numeric($postId)) {
            $post = $this->postRepository->find($postId);
            if ($post !== null) {
                return true;
            }
        }
        $this->addFlash('danger', "Ce Post n'existe pas !");
        return false;
    }

    public function doCommentExist($commentId): bool
    {
        if (is_numeric($commentId)) {
            $comment = $this->commentRepository->find($commentId);
            if ($comment !== null) {
                return true;
            }
        }
        $this->addFlash('danger', "Ce Commentaire n'existe pas !");
        return false;
    }

    public function isAuthorOfComment($commentId): bool
    {
        if ($this->doCommentExist($commentId)) {
            $comment = $this->commentRepository->find($commentId);
            if ($this->isGranted('ROLE_USER') && $this->tokenStorage->getToken()->getUser() === $comment->getAuthor()) {
                return true;
            }
        }
        $this->addFlash('danger', "Vous n'êtes pas l'auteur de ce Commentaire !");
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
     * Comment Creation
     */
    #[Route('create/{postId}', name: 'create')]
    public function createComment($postId, Request $request): Response
    {
        // Security
        if (!$this->doPostExist($postId)) {
            return $this->redirectToRoute('home');
        }
        // Form & Comment
        $form = $this->commentFormBuilder->getCreateForm($postId);
        $comment = $this->commentFormHandler->handle($request, $form);
        // Valid Form Redirect
        if ($comment !== null) {
            $this->addFlash('success', 'Votre Commentaire à bien été crée !');
            return $this->redirectToRoute('post_show', ['postId' => $postId]);
        }
        // Invalid Form Flash
        if ($form->isSubmitted()) {
            $this->addFlash('warning', 'Le formulaire est invalide !');
        }
        //Render
        return $this->render('comment/comment-form.html.twig', [
            'form' => $form->createView(),
            'postId' => $postId,
        ]);
    }

    /**
     * Comment Modification
     */
    #[Route('modify/{commentId}', name: 'modify')]
    public function modifyComment($commentId, Request $request): Response
    {
        // Security
        if (!$this->doCommentExist($commentId) || !$this->isAuthorOfComment($commentId)) {
            return $this->redirectToRoute('home');
        }
        // Form & Comment
        $form = $this->commentFormBuilder->getModifyForm($commentId);
        $comment = $this->commentFormHandler->handle($request, $form);
        // Modification Redirect
        if ($comment !== null) {
            $this->addFlash('success', 'Le Commentaire à bien été modifié !');
            return $this->redirectToRoute('post_show', ['postId' => $this->getPostId($commentId)]);
        }
        // Render
        return $this->render('comment/comment-form.html.twig', [
            'form' => $form->createView(),
            'commentId' => $commentId,
        ]);
    }

    /**
     * Comment Removal
     */
    #[Route('delete/{commentId}', name: 'delete')]
    public function deleteComment($commentId): Response
    {
        // Exist Security
        if (!$this->doCommentExist($commentId) || !$this->isAuthorOfComment($commentId) && !$this->isAdmin()) {
            return $this->redirectToRoute('home');
        }
        // PostId for Redirection after removal
        $postId = $this->getPostId($commentId);
        // Delete Comment
        $this->commentManager->delete($this->commentRepository->find($commentId));
        $this->addFlash('success', 'Le Commentaire à bien été supprimé !');
        // Redirect to Post
        return $this->redirectToRoute('post_show', ['postId' => $postId]);
    }

    /**
     * Cancel Comment Creation
     */
    #[Route('create/{postId}/cancel', name: 'create_cancel')]
    public function cancelCommentCreation($postId): Response
    {
        // Security
        if (!$this->doPostExist($postId)) {
            return $this->redirectToRoute('home');
        }
        // Redirect to Post
        $this->addFlash('secondary', 'Création du Commentaire annulée !');
        return $this->redirectToRoute('post_show', ['postId' => $postId]);
    }

    /**
     * Cancel Comment Modification
     */
    #[Route('modify/{commentId}/cancel', name: 'modify_cancel')]
    public function cancelCommentModification($commentId): Response
    {
        // Security
        if (!$this->doCommentExist($commentId) || !$this->isAuthorOfComment($commentId)) {
            return $this->redirectToRoute('home');
        }
        // Redirect to Post
        $this->addFlash('secondary', 'Modification du Commentaire annulée !');
        return $this->redirectToRoute('post_show', ['postId' => $this->getPostId($commentId)]);
    }

    /**
     * Cancel Comment Removal
     */
    #[Route('remove/{commentId}/cancel', name: 'remove_cancel')]
    public function cancelCommentRemoval($commentId): Response
    {
        // Security
        if (!$this->doCommentExist($commentId) || !$this->isAuthorOfComment($commentId) && !$this->isAdmin()) {
            return $this->redirectToRoute('home');
        }
        // Redirect to Post
        $this->addFlash('secondary', 'Suppression du Commentaire annulée !');
        return $this->redirectToRoute('post_show', ['postId' => $this->getPostId($commentId)]);
    }
}
