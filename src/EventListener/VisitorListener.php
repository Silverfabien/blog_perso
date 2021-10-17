<?php

namespace App\EventListener;

use App\Entity\User\User;
use App\Entity\Visitor\Visitor;
use App\EventListener\Handler\VisitorHandler;
use App\Repository\Visitor\VisitorRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\Security\Core\Security;

class VisitorListener
{
    private $visitorRepository;
    private $security;
    private $request;
    private $visitorHandler;
    private $profiler;

    public function __construct(
        VisitorRepository $visitorRepository,
        Security $security,
        Request $request,
        VisitorHandler $visitorHandler,
        Profiler $profiler
    )
    {
        $this->visitorRepository = $visitorRepository;
        $this->security = $security;
        $this->request = $request;
        $this->visitorHandler = $visitorHandler;
        $this->profiler = $profiler;
    }

    public function onKernelRequest(
        RequestEvent $event
    )
    {
        /* @var $user User */
        $user = $this->security->getUser();
        $ip = $_SERVER['REMOTE_ADDR'];
        $visitorUserExist = $this->visitorRepository->findOneByUser($user);
        $visitorIpExist = $this->visitorRepository->findOneByIp($ip);

        $this->profiler->disable();

        if ($user && !$visitorUserExist) {
            $visitor = new Visitor();

            if ($this->visitorHandler->newIfUserConnected($visitor, $user, $event)) {
                $this->profiler->enable();
                return;
            }
        }

        if ($user && $visitorUserExist && $this->visitorHandler->updateIfUserConnected($visitorUserExist, $event)) {
            $this->profiler->enable();
            return;
        }

        if (!$user && $visitorIpExist && $this->visitorHandler->updateIfIpExistAndUserDisconnect($visitorIpExist, $event)) {
            $this->profiler->enable();
            return;
        }

        if (!$visitorIpExist) {
            $visitor = new Visitor();
            if ($this->visitorHandler->updateIfIpNotExist($visitor, $event)) {
                $this->profiler->enable();
                return;
            }
        }

        $this->profiler->enable();

        return;
    }
}