<?php

namespace App\ControllerHandler\Admin\Contact;

use App\Entity\Contact\Contact;
use App\Repository\Contact\ContactRepository;

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
     * @param Contact $contact
     * @return bool
     */
    public function seeContactHandle(
        Contact $contact
    ): bool
    {
        $contact->setView(true);

        $this->contactRepository->update($contact);

        return true;
    }

    /**
     * @param Contact $contact
     * @return bool
     */
    public function confirmContactHandle(
        Contact $contact
    ): bool
    {
        $contact->setConfirm(true);

        $this->contactRepository->update($contact);

        return true;
    }

    /**
     * @param Contact $contact
     * @return bool
     */
    public function unconfirmContactHandle(
        Contact $contact
    ): bool
    {
        $contact->setConfirm(false);

        $this->contactRepository->update($contact);

        return true;
    }
}
