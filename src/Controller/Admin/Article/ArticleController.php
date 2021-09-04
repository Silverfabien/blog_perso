<?php

namespace App\Controller\Admin\Article;

use App\ControllerHandler\Admin\Article\ArticleHandler;
use App\Entity\Article\Article;
use App\Form\Admin\Article\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/admin/article", name="admin_article_")
 */
class ArticleController extends AbstractController
{
    /**
     * @param Request $request
     * @param ArticleHandler $articleHandler
     * @param UserInterface $user
     * @return Response
     *
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(
        Request $request,
        ArticleHandler $articleHandler,
        UserInterface $user
    ): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article)->handleRequest($request);

        if ($articleHandler->createArticleHandle($form, $article, $user)) {
            $this->addFlash(
                'success',
                sprintf(
                    "L'ajout de l'article \"%s\" à bien été effectué, il est en attente de validation.",
                    $article->getTitle()
                )
            );
            // TODO redirect
            return $this->redirectToRoute('default');
        }

        return $this->render('admin/article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Article $article
     * @return Response
     *
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(
        Article $article
    ): Response
    {
        return $this->render('admin/article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @param Request $request
     * @param Article $article
     * @param ArticleHandler $articleHandler
     * @param UserInterface $user
     * @return Response
     *
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Article $article,
        ArticleHandler $articleHandler,
        UserInterface $user
    ): Response
    {
        $form = $this->createForm(ArticleType::class, $article)->handleRequest($request);

        if ($articleHandler->editArticleHandle($form, $article, $user)) {
            $this->addFlash(
                'success',
                sprintf(
                    "L'édition de l'article \"%s\" à bien été effectué.",
                    $article->getTitle()
                )
            );

            return $this->redirectToRoute('default');
        }

        return $this->render('admin/article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        Article $article,
        ArticleHandler $articleHandler
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articleHandler->deleteArticleHandle($article);

            $this->addFlash(
                'success',
                sprintf(
                    "La suppression de l'article \"%s\" à bien été effectué.",
                    $article->getTitle()
                )
            );
        }

        return $this->redirectToRoute('default');
    }
}