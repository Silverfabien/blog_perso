<?php

namespace App\Controller\Contact;

use App\ControllerHandler\Contact\ContactHandler;
use App\Entity\Contact\Contact;
use App\Form\Contact\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ContactController
 */
class ContactController extends AbstractController
{
    /**
     * @param Request $request
     * @param ContactHandler $contactHandler
     * @return Response
     *
     * @Route("/contact", name="contact")
     */
    public function new(
        Request $request,
        ContactHandler $contactHandler
    ): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact)->handleRequest($request);

        if ($contactHandler->newContactHandle($form, $contact)) {
            $this->addFlash("success", "Votre message a bien été envoyé");

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
