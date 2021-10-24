<?php

namespace App\Controller\Admin\Article;

use App\ControllerHandler\Admin\Article\ArticleHandler;
use App\Entity\Article\Article;
use App\Form\Admin\Article\ArticleType;
use App\Repository\Article\ArticleRepository;
use App\Repository\Article\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ArticleController
 *
 * @Route("/admin/article", name="admin_article_")
 */
class ArticleController extends AbstractController
{
    private ArticleHandler $articleHandler;

    public function __construct(
        ArticleHandler $articleHandler
    )
    {
        $this->articleHandler = $articleHandler;
    }

    /**
     * @param ArticleRepository $articleRepository
     * @return Response
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(
        ArticleRepository $articleRepository
    ): Response
    {
        return $this->render('admin/article/index.html.twig', [
            'articles' => $articleRepository->findAll()
        ]);
    }

    /**
     * @param UserInterface $user
     * @param Request $request
     * @return Response
     *
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(
        UserInterface $user,
        Request $request
    ): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article)->handleRequest($request);

        if ($this->articleHandler->createArticleHandle($form, $article, $user)) {
            $this->addFlash(
                'success',
                sprintf(
                    "L'ajout de l'article \"%s\" à bien été effectué, il est en attente de validation.",
                    $article->getTitle()
                )
            );
            return $this->redirectToRoute('admin_article_index');
        }

        return $this->render('admin/article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param CommentRepository $commentRepository
     * @param Article $article
     * @return Response
     *
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(
        CommentRepository $commentRepository,
        Article $article
    ): Response
    {
        return $this->render('admin/article/show.html.twig', [
            'article' => $article,
            'nbComment' => count($commentRepository->findBy(['article' => $article]))
        ]);
    }

    /**
     * @param UserInterface $user
     * @param Request $request
     * @param Article $article
     * @return Response
     *
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(
        UserInterface $user,
        Request $request,
        Article $article
    ): Response
    {
        $form = $this->createForm(ArticleType::class, $article)->handleRequest($request);

        if ($this->articleHandler->editArticleHandle($form, $article, $user)) {
            $this->addFlash(
                'success',
                sprintf(
                    "L'édition de l'article \"%s\" à bien été effectué.",
                    $article->getTitle()
                )
            );

            return $this->redirectToRoute('admin_article_index');
        }

        return $this->render('admin/article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Article $article
     * @return Response
     *
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        Article $article
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $this->articleHandler->deleteArticleHandle($article);

            $this->addFlash(
                'success',
                sprintf(
                    "La suppression de l'article \"%s\" à bien été effectué.",
                    $article->getTitle()
                )
            );
        }

        return $this->redirectToRoute('admin_article_index');
    }

    /**
     * @param Request $request
     * @param Article $article
     * @return Response
     *
     * @Route("/{id}/publishing", name="publishing", methods={"POST"})
     */
    public function publishing(
        Request $request,
        Article $article
    ): Response
    {
        if ($this->isCsrfTokenValid('publishing'.$article->getId(), $request->request->get('_token'))) {
            $this->articleHandler->publishHandle($article);

            $this->addFlash('success', "L'article a bien été publié");
        }

        return $this->redirectToRoute('admin_article_index');
    }

    /**
     * @param Request $request
     * @param Article $article
     * @return Response
     *
     * @Route("/{id}/unpublishing", name="unpublishing", methods={"POST"})
     */
    public function unpublishing(
        Request $request,
        Article $article
    ): Response
    {
        if ($this->isCsrfTokenValid('unpublishing'.$article->getId(), $request->request->get('_token'))) {
            $this->articleHandler->unpublishHandle($article);

            $this->addFlash('success', "L'article a bien été dépublié");
        }

        return $this->redirectToRoute('admin_article_index');
    }
}
