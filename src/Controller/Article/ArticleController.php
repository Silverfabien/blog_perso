<?php

namespace App\Controller\Article;

use App\ControllerHandler\Admin\Article\ArticleHandler;
use App\ControllerHandler\Article\ArticleCommentHandler;
use App\Entity\Article\Article;
use App\Entity\Article\Comment;
use App\Entity\Article\Like;
use App\Entity\User\User;
use App\Form\Article\ArticleCommentType;
use App\Form\Article\ArticleEditCommentType;
use App\Repository\Article\ArticleRepository;
use App\Repository\Article\CommentRepository;
use App\Repository\Article\LikeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article", name="article_")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
        LikeRepository $likeRepository
    ): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
            'comments' => $commentRepository->findAll(),
            'likes' => $likeRepository->findAll()
        ]);
    }

    /**
     * @Route("/{slug}", name="show")
     */
    public function show(
        Article $article,
        LikeRepository $likeRepository,
        ArticleHandler $articleHandler,
        Request $request,
        ArticleCommentHandler $commentHandler,
        CommentRepository $commentRepository,
        Comment $comment
    )
    {
        /* @var $user User */
        $user = $this->getUser();

        if (!$article->getPublish() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('article_index');
        }

        // Formulaire des commentaires
        $commentArticle = new Comment();
        $form = $this->createForm(ArticleCommentType::class, $commentArticle)->handleRequest($request);

        if ($this->getUser() && $commentHandler->createCommentHandle($form, $commentArticle, $article, $user)) {
            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        // Count visitor
        if ($article->getPublish()) {
            $articleHandler->seeArticleHandle($article);
        }

        // Édition d'un commentaire
        $editForm = $this->createForm(ArticleEditCommentType::class, $comment)->handleRequest($request);
        if ($commentHandler->editCommentHandle($editForm, $comment)) {
            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'likes' => $likeRepository->findByArticle($article),
            'articleLike' => count($likeRepository->findByArticle($article)),
            'form' => $form->createView(),
            'comments' => $commentRepository->findByArticle($article),
            'countComment' => count($commentRepository->findBy(['article' => $article, 'deleted' => false])),
            'editForm' => $editForm->createView()
        ]);
    }

    /**
     * @param Request $request
     *
     * @Route("/{slug}/editComment/{id}", name="edit_comment")
     */
    public function editComment(
        Request $request,
        CommentRepository $commentRepository
    )
    {
        //TODO A debug
        if ($this->getUser() && $request->getMethod() === 'POST' && $request->isXmlHttpRequest()) {
            $commentId = $request->request->get('commentId');
            $comment = $commentRepository->findOneById($commentId);

            $submittedToken = $request->request->get('csrfToken');
            if ($this->isCsrfTokenValid('comment' . $comment->getId(), $submittedToken)) {
                $form = $this->createForm(ArticleEditCommentType::class, $comment)->handleRequest($request);

                return new JsonResponse($this->renderView('article/comment/_modal.html.twig', [
                    'editForm' => $form->createView()
                ]));
            }
        }

        return new JsonResponse();
    }

    /**
     * @param Request $request
     * @param ArticleHandler $articleHandler
     *
     * @Route("/{slug}/like", name="like_unlike")
     */
    public function likeUnlike(
        Request $request,
        ArticleHandler $articleHandler,
        LikeRepository $likeRepository
    ): JsonResponse
    {
        if ($this->getUser() && $request->getMethod() === 'POST' && $request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $articleSlug = $request->request->get('entitySlug');
            $article = $em->getRepository(Article::class)->findOneBySlug([$articleSlug]);

            if (!$article) {
                return new JsonResponse();
            }

            $submittedToken = $request->request->get('csrfToken');

            if ($this->isCsrfTokenValid('article' . $article->getSlug(), $submittedToken)) {
                /* @var $user User */
                $user = $this->getUser();
                $articleAlreadyLiked = $em->getRepository(Like::class)->findOneBy([
                    'user' => $user,
                    'article' => $article
                ]);

                if ($articleAlreadyLiked) {
                    $likeRepository->unlike($articleAlreadyLiked);
                    $result = 'delete';
                } else {
                    $like = new Like();
                    $articleHandler->likeHandle($like, $article, $user);
                    $result = 'create';
                }
            }
        }

        return new JsonResponse($result);
    }
}
