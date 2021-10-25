<?php

namespace App\EventListener;

use App\Entity\User\User;
use App\Entity\Visitor\Visitor;
use App\EventListener\Handler\VisitorHandler;
use App\Repository\Visitor\VisitorRepository;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;

class VisitorListener
{
    private VisitorRepository $visitorRepository;
    private Security $security;
    private VisitorHandler $visitorHandler;

    public function __construct(
        VisitorRepository $visitorRepository,
        Security $security,
        VisitorHandler $visitorHandler
    )
    {
        $this->visitorRepository = $visitorRepository;
        $this->security = $security;
        $this->visitorHandler = $visitorHandler;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(
        RequestEvent $event
    ): void
    {
        /* @var $user User */
        $user = $this->security->getUser();
        $ip = $_SERVER['REMOTE_ADDR'];
        $visitorUserExist = $this->visitorRepository->findOneBy(['user' => $user]);
        $visitorIpExist = $this->visitorRepository->findOneBy(['ip' => $ip]);

        if ($user && !$visitorUserExist) {
            $visitor = new Visitor();
            $this->visitorHandler->newIfUserConnected($visitor, $user, $event);
        }
        // TODO A revoir pour debug en dev
        if ($user && $visitorUserExist) {
            $this->visitorHandler->updateIfUserConnected($visitorUserExist, $event);
        }

        if (!$user && $visitorIpExist  ) {
            $this->visitorHandler->updateIfIpExistAndUserDisconnect($visitorIpExist, $event);
        }

        if (!$visitorIpExist) {
            $visitor = new Visitor();
            $this->visitorHandler->updateIfIpNotExist($visitor, $event);
        }
    }
}
