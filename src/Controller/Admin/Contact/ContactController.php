<?php

namespace App\Controller\Admin\Contact;

use App\ControllerHandler\Admin\Contact\ContactHandler;
use App\Entity\Contact\Contact;
use App\Repository\Contact\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ContactController
 *
 * @Route("/admin/contact", name="admin_contact_")
 */
class ContactController extends AbstractController
{
    private Request $request;
    private Contact $contact;
    private ContactHandler $contactHandler;

    public function __construct(
        Request $request,
        Contact $contact,
        ContactHandler $contactHandler
    )
    {
        $this->request = $request;
        $this->contact = $contact;
        $this->contactHandler = $contactHandler;
    }

    /**
     * @param ContactRepository $contactRepository
     * @return Response
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(
        ContactRepository $contactRepository
    ): Response
    {
        return $this->render('admin/contact/index.html.twig', [
            'contacts' => $contactRepository->findAll()
        ]);
    }

    /**
     * @return Response
     *
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(): Response
    {
        if (!$this->contact->getView()) {
            $this->contactHandler->seeContactHandle($this->contact);
        }

        return $this->render('admin/contact/show.html.twig', [
            'contact' => $this->contact
        ]);
    }

    /**
     * @return Response
     *
     * @Route("/{id}/confirm", name="confirm", methods={"GET", "POST"})
     */
    public function confirm(): Response
    {
        if ($this->isCsrfTokenValid('confirm'.$this->contact->getId(), $this->request->request->get('_token'))) {
            $this->contactHandler->confirmContactHandle($this->contact);

            $this->addFlash("success", "La demande a bien été validé.");
        }

        return $this->redirectToRoute('admin_contact_index');
    }

    /**
     * @return Response
     *
     * @Route("/{id}/unconfirm", name="unconfirm", methods={"GET", "POST"})
     */
    public function unconfirm(): Response
    {
        if ($this->isCsrfTokenValid('unconfirm'.$this->contact->getId(), $this->request->request->get('_token'))) {
            $this->contactHandler->unconfirmContactHandle($this->contact);

            $this->addFlash("success", "La validation de la demande a bien été retiré.");
        }

        return $this->redirectToRoute('admin_contact_index');
    }
}
