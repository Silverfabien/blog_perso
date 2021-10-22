<?php

namespace App\Controller\Article;

use App\ControllerHandler\Article\ArticleHandler;
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
 * Class ArticleController
 *
 * @Route("/article", name="article_")
 */
class ArticleController extends AbstractController
{
    private ArticleRepository $articleRepository;
    private CommentRepository $commentRepository;
    private LikeRepository $likeRepository;
    private ArticleHandler $articleHandler;
    private Request $request;

    public function __construct(
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
        LikeRepository $likeRepository,
        ArticleHandler $articleHandler,
        Request $request
    )
    {
        $this->articleRepository = $articleRepository;
        $this->commentRepository = $commentRepository;
        $this->likeRepository = $likeRepository;
        $this->articleHandler = $articleHandler;
        $this->request = $request;
    }

    /**
     * @return Response
     *
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $this->articleRepository->findAll(),
            'comments' => $this->commentRepository->findAll(),
            'likes' => $this->likeRepository->findAll()
        ]);
    }

    /**
     * @param Article $article
     * @param ArticleCommentHandler $commentHandler
     * @return Response
     *
     * @Route("/{slug}", name="show")
     */
    public function show(
        Article $article,
        ArticleCommentHandler $commentHandler
    ): Response
    {
        /* @var $user User */
        $user = $this->getUser();

        if (!$article->getPublish() && !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('article_index');
        }

        // Formulaire des commentaires
        $commentArticle = new Comment();
        $form = $this->createForm(ArticleCommentType::class, $commentArticle)->handleRequest($this->request);

        if ($this->getUser() && $commentHandler->createCommentHandle($form, $commentArticle, $article, $user)) {
            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
        }

        // Count visitor
        if ($article->getPublish()) {
            $this->articleHandler->seeArticleHandle($article);
        }

//        // Édition d'un commentaire
//        $editForm = $this->createForm(ArticleEditCommentType::class, $commentArticle)->handleRequest($this->request);
//        if ($commentHandler->editCommentHandle($editForm, $commentArticle)) {
//            return $this->redirectToRoute('article_show', ['slug' => $article->getSlug()]);
//        }

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'likes' => $this->likeRepository->findBy(['article' => $article]),
            'articleLike' => count($this->likeRepository->findBy(['article' => $article])),
            'form' => $form->createView(),
            'comments' => $this->commentRepository->findBy(['article' => $article]),
            'countComment' => count($this->commentRepository->findBy(['article' => $article, 'deleted' => false]))
        ]);
    }

    /**
     * @param ArticleCommentHandler $commentHandler
     * @return Response
     *
     * @Route("/{slug}/editComment/{id}", name="edit_comment")
     */
    public function editComment(
        ArticleCommentHandler $commentHandler
    ): Response
    {
        //TODO A debug
        if ($this->getUser() && $this->request->getMethod() === 'POST' && $this->request->isXmlHttpRequest()) {
            $commentId = $this->request->request->get('commentId');
            $comment = $this->commentRepository->findOneBy(['id' => $commentId]);

            $submittedToken = $this->request->request->get('csrfToken');
            if (!$comment && $this->isCsrfTokenValid('comment'.$comment->getId(), $submittedToken)) {
                $form = $this->createForm(ArticleEditCommentType::class, $comment)->handleRequest($this->request);

                if ($commentHandler->editCommentHandle($form, $comment)) {
                    return $this->redirectToRoute('article_index');
                }

                return new JsonResponse($this->renderView('article/comment/_modal.html.twig', [
                    'editForm' => $form->createView()
                ]));
            }
        }

        return new JsonResponse();
    }

    /**
     * @return JsonResponse
     *
     * @Route("/{slug}/like", name="like_unlike")
     */
    public function likeUnlike(): JsonResponse
    {
        $result = '';

        if ($this->getUser() && $this->request->getMethod() === 'POST' && $this->request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $articleSlug = $this->request->request->get('entitySlug');
            $article = $em->getRepository(Article::class)->findOneBy(['slug' => $articleSlug]);

            if (!$article) {
                return new JsonResponse();
            }

            $submittedToken = $this->request->request->get('csrfToken');

            if ($this->isCsrfTokenValid('article' . $article->getSlug(), $submittedToken)) {
                /* @var $user User */
                $user = $this->getUser();
                $articleAlreadyLiked = $em->getRepository(Like::class)->findOneBy([
                    'user' => $user,
                    'article' => $article
                ]);

                if ($articleAlreadyLiked) {
                    $this->likeRepository->unlike($articleAlreadyLiked);
                    $result = 'delete';
                } else {
                    $like = new Like();
                    $this->articleHandler->likeHandle($like, $article, $user);
                    $result = 'create';
                }
            }
        }

        return new JsonResponse($result);
    }
}
