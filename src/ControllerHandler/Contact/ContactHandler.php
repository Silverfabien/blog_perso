<?php

namespace App\ControllerHandler\Contact;

use App\Entity\Contact\Contact;
use App\Repository\Contact\ContactRepository;
use Symfony\Component\Form\FormInterface;

class ContactHandler
{
    private ContactRepository $contactRepository;

    public function __construct(
        ContactRepository $contactRepository
    )
    {
        $this->contactRepository = $contactRepository;
    }

    /**
     * @param FormInterface $form
     * @param Contact $contact
     * @return bool
     */
    public function newContactHandle(
        FormInterface $form,
        Contact $contact
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $this->contactRepository->save($contact);

            return true;
        }

        return false;
    }
}
