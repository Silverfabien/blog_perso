<?php

namespace App\Controller\Admin\User;

use App\ControllerHandler\Admin\User\BlockedHandler;
use App\Entity\User\Blocked;
use App\Entity\User\User;
use App\Form\Admin\User\BlockedType;
use App\Form\Admin\User\UnblockedType;
use App\Repository\User\BlockedRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/blocked", name="admin_blocked_")
 */
class BlockedController extends AbstractController
{
    private Request $request;
    private BlockedHandler $blockedHandler;
    private Blocked $blocked;
    private User $user;

    public function __construct(
        Request $request,
        BlockedHandler $blockedHandler,
        Blocked $blocked,
        User $user
    )
    {
        $this->request = $request;
        $this->blockedHandler = $blockedHandler;
        $this->blocked = $blocked;
        $this->user = $user;
    }

    /**
     * @param BlockedRepository $blockedRepository
     * @return Response
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(
        BlockedRepository $blockedRepository
    ): Response
    {
        return $this->render('admin/user/blocked/index.html.twig', [
            'blockeds' => $blockedRepository->findAll()
        ]);
    }

    /**
     * @return Response
     *
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(): Response
    {
        return $this->render('admin/user/blocked/show.html.twig', [
            'blocked' => $this->blocked
        ]);
    }

    /**
     * @return Response
     *
     * @Route("/{user_id}/blocked", name="blocked", methods={"GET", "POST"})
     * @ParamConverter("user", options={"id" = "user_id"})
     */
    public function blocked(): Response
    {
        $blocked = new Blocked();
        $form = $this->createForm(BlockedType::class, $blocked)->handleRequest($this->request);

        if ($this->blockedHandler->blockedHandle($form, $blocked, $this->user)) {
            $this->addFlash('success', "Le compte a bien été bloqué");

            return $this->redirectToRoute('admin_blocked_index');
        }

        return $this->render('admin/user/blocked/blocked.html.twig', [
            'form' => $form->createView(),
            'user' => $this->user
        ]);
    }

    /**
     * @return Response
     *
     * @Route("/{user_id}/unblocked", name="unblocked", methods={"GET", "POST"})
     * @ParamConverter("user", options={"id" = "user_id"})
     */
    public function unblocked(): Response
    {
        $form = $this->createForm(UnblockedType::class, $this->blocked)->handleRequest($this->request);

        if ($this->blockedHandler->unblockedHandle($form, $this->blocked)) {
            $this->addFlash('success', "Le compte a bien été débloqué");

            return $this->redirectToRoute('admin_blocked_index');
        }

        return $this->render('admin/user/blocked/unblocked.html.twig', [
            'form' => $form->createView(),
            'user' => $this->user
        ]);
    }
}
