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
    private Request $request;
    private Tags $tags;
    private TagsHandler $tagsHandler;

    public function __construct(
        Request $request,
        Tags $tag,
        TagsHandler $tagsHandler
    )
    {
        $this->request = $request;
        $this->tags = $tag;
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
     * @return Response
     *
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(): Response
    {
        $tag = new Tags();
        $form = $this->createForm(TagsType::class, $tag)->handleRequest($this->request);

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
     * @return Response
     *
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(): Response
    {
        return $this->render('admin/article/tags/show.html.twig', [
            'tag' => $this->tags
        ]);
    }

    /**
     * @return Response
     *
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(): Response
    {
        $form = $this->createForm(TagsType::class, $this->tags)->handleRequest($this->request);

        if ($this->tagsHandler->editTagsHandle($form, $this->tags)) {
            $this->addFlash(
                'success',
                sprintf(
                    "L'édition du tag \"%s\" à bien été effectué.",
                    $this->tags->getName()
                )
            );

            return $this->redirectToRoute('admin_tag_index');
        }

        return $this->render('admin/article/tags/edit.html.twig', [
            'tag' => $this->tags,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     *
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(): Response
    {
        if ($this->isCsrfTokenValid('delete'.$this->tags->getId(), $this->request->request->get('_token'))) {
            $this->tagsHandler->deleteTasHandle($this->tags);

            $this->addFlash(
                'success',
                sprintf(
                    "La suppression du tag \"%s\" à bien été effectué.",
                    $this->tags->getName()
                )
            );
        }

        return $this->redirectToRoute('admin_tag_index');
    }
}
