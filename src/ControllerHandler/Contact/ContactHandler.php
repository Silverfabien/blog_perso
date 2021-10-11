<?php

namespace App\ControllerHandler\Contact;

use App\Entity\Contact\Contact;
use App\Repository\Contact\ContactRepository;
use Symfony\Component\Form\FormInterface;

class ContactHandler
{
    private $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }


    public function newContactHandle(
        FormInterface $form,
        Contact $contact
    )
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $this->contactRepository->save($contact);

            return true;
        }

        return false;
    }
}
