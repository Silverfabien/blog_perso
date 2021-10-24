<?php

namespace App\Controller\Admin\Article;

use App\ControllerHandler\Admin\Article\TagsHandler;
use App\Entity\Article\Tags;
use App\Form\Admin\Article\TagsType;
use App\Repository\Article\TagsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TagsController
 *
 * @Route("/admin/tag", name="admin_tag_")
 */
class TagsController extends AbstractController
{
    private TagsHandler $tagsHandler;

    public function __construct(
        TagsHandler $tagsHandler
    )
    {
        $this->tagsHandler = $tagsHandler;
    }

    /**
     * @param TagsRepository $tagsRepository
     * @return Response
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(
        TagsRepository $tagsRepository
    ): Response
    {
        return $this->render('admin/article/tags/index.html.twig', [
            'tags' => $tagsRepository->findAll()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(
        Request $request
    ): Response
    {
        $tag = new Tags();
        $form = $this->createForm(TagsType::class, $tag)->handleRequest($request);

        if ($this->tagsHandler->createTagsHandle($form, $tag)) {
            $this->addFlash(
                'success',
                sprintf(
                    "L'ajout du tag \"%s\" à bien été effectué.",
                    $tag->getName()
                )
            );
            return $this->redirectToRoute('admin_tag_index');
        }

        return $this->render('admin/article/tags/new.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Tags $tag
     * @return Response
     *
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(
        Tags $tag
    ): Response
    {
        return $this->render('admin/article/tags/show.html.twig', [
            'tag' => $tag
        ]);
    }

    /**
     * @param Request $request
     * @param Tags $tag
     * @return Response
     *
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Tags $tag
    ): Response
    {
        $form = $this->createForm(TagsType::class, $tag)->handleRequest($request);

        if ($this->tagsHandler->editTagsHandle($form, $tag)) {
            $this->addFlash(
                'success',
                sprintf(
                    "L'édition du tag \"%s\" à bien été effectué.",
                    $tag->getName()
                )
            );

            return $this->redirectToRoute('admin_tag_index');
        }

        return $this->render('admin/article/tags/edit.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Tags $tag
     * @return Response
     *
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(
        Request $request,
        Tags $tag
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->request->get('_token'))) {
            $this->tagsHandler->deleteTasHandle($tag);

            $this->addFlash(
                'success',
                sprintf(
                    "La suppression du tag \"%s\" à bien été effectué.",
                    $tag->getName()
                )
            );
        }

        return $this->redirectToRoute('admin_tag_index');
    }
}
