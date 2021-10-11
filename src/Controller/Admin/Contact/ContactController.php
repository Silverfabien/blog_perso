<?php

namespace App\Controller\Admin\Contact;

use App\ControllerHandler\Admin\Contact\ContactHandler;
use App\Entity\Contact\Contact;
use App\Repository\Contact\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/contact", name="admin_contact_")
 */
class ContactController extends AbstractController
{
    /**
     * @param ContactRepository $contactRepository
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(
        ContactRepository $contactRepository
    )
    {
        return $this->render('admin/contact/index.html.twig', [
            'contacts' => $contactRepository->findAll()
        ]);
    }

    /**
     * @Route("/{id}/show", name="show", methods={"GET"})
     */
    public function show(
        Contact $contact,
        ContactHandler $contactHandler
    )
    {
        if (!$contact->getView()) {
            $contactHandler->seeContactHandle($contact);
        }

        return $this->render('admin/contact/show.html.twig', [
            'contact' => $contact
        ]);
    }

    /**
     * @Route("/{id}/confirm", name="confirm", methods={"GET", "POST"})
     */
    public function confirm(
        Request $request,
        Contact $contact,
        ContactHandler $contactHandler
    )
    {
        if ($this->isCsrfTokenValid('confirm'.$contact->getId(), $request->request->get('_token'))) {
            $contactHandler->confirmContactHandle($contact);

            $this->addFlash("success", "La demande a bien été validé.");
        }

        return $this->redirectToRoute('admin_contact_index');
    }

    /**
     * @Route("/{id}/unconfirm", name="unconfirm", methods={"GET", "POST"})
     */
    public function unconfirm(
        Request $request,
        Contact $contact,
        ContactHandler $contactHandler
    )
    {
        if ($this->isCsrfTokenValid('unconfirm'.$contact->getId(), $request->request->get('_token'))) {
            $contactHandler->unconfirmContactHandle($contact);

            $this->addFlash("success", "La validation de la demande a bien été retiré.");
        }

        return $this->redirectToRoute('admin_contact_index');
    }
}
