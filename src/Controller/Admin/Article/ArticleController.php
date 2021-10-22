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
    private Request $request;
    private Article $article;
    private ArticleHandler $articleHandler;
    private UserInterface $user;

    public function __construct(
        Request $request,
        Article $article,
        ArticleHandler $articleHandler,
        UserInterface $user
    )
    {
        $this->request = $request;
        $this->article = $article;
        $this->articleHandler = $articleHandler;
        $this->user = $user;
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
     * @return Response
     *
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article)->handleRequest($this->request);

        if ($this->articleHandler->createArticleHandle($form, $article, $this->user)) {
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
     * @return Response
     *
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(
        CommentRepository $commentRepository
    ): Response
    {
        return $this->render('admin/article/show.html.twig', [
            'article' => $this->article,
            'nbComment' => count($commentRepository->findBy(['article' => $this->article]))
        ]);
    }

    /**
     * @return Response
     *
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(): Response
    {
        $form = $this->createForm(ArticleType::class, $this->article)->handleRequest($this->request);

        if ($this->articleHandler->editArticleHandle($form, $this->article, $this->user)) {
            $this->addFlash(
                'success',
                sprintf(
                    "L'édition de l'article \"%s\" à bien été effectué.",
                    $this->article->getTitle()
                )
            );

            return $this->redirectToRoute('admin_article_index');
        }

        return $this->render('admin/article/edit.html.twig', [
            'article' => $this->article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     *
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(): Response
    {
        if ($this->isCsrfTokenValid('delete'.$this->article->getId(), $this->request->request->get('_token'))) {
            $this->articleHandler->deleteArticleHandle($this->article);

            $this->addFlash(
                'success',
                sprintf(
                    "La suppression de l'article \"%s\" à bien été effectué.",
                    $this->article->getTitle()
                )
            );
        }

        return $this->redirectToRoute('admin_article_index');
    }

    /**
     * @return Response
     *
     * @Route("/{id}/publishing", name="publishing", methods={"POST"})
     */
    public function publishing(): Response
    {
        if ($this->isCsrfTokenValid('publishing'.$this->article->getId(), $this->request->request->get('_token'))) {
            $this->articleHandler->publishHandle($this->article);

            $this->addFlash('success', "L'article a bien été publié");
        }

        return $this->redirectToRoute('admin_article_index');
    }

    /**
     * @return Response
     *
     * @Route("/{id}/unpublishing", name="unpublishing", methods={"POST"})
     */
    public function unpublishing(): Response
    {
        if ($this->isCsrfTokenValid('unpublishing'.$this->article->getId(), $this->request->request->get('_token'))) {
            $this->articleHandler->unpublishHandle($this->article);

            $this->addFlash('success', "L'article a bien été dépublié");
        }

        return $this->redirectToRoute('admin_article_index');
    }
}
