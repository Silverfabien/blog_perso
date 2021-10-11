<?php

namespace App\ControllerHandler\Admin\Contact;

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


    public function seeContactHandle(
        Contact $contact
    )
    {
        $contact->setView(true);

        $this->contactRepository->update($contact);

        return true;
    }

    public function confirmContactHandle(
        Contact $contact
    )
    {
        $contact->setConfirm(true);

        $this->contactRepository->update($contact);

        return true;
    }

    public function unconfirmContactHandle(
        Contact $contact
    )
    {
        $contact->setConfirm(false);

        $this->contactRepository->update($contact);

        return true;
    }
}
