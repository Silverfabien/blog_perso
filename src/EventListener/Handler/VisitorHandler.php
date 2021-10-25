<?php

namespace App\EventListener\Handler;

use App\Entity\Visitor\Visitor;
use App\Repository\Visitor\VisitorRepository;
use DateTimeImmutable;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class VisitorHandler
{
    protected const ROUTE_DEV_BAR_SYMFONY = "_wdt";

    private VisitorRepository $visitorRepository;

    public function __construct(
        VisitorRepository $visitorRepository
    )
    {
        $this->visitorRepository = $visitorRepository;
    }

    public function browser(): string
    {
        $httpUserAgent = $_SERVER['HTTP_USER_AGENT'];
        $browser = 'Inconnu';

        $browserArray = [
            '/mobile/i'    => 'Handheld Browser',
            '/msie/i'      => 'Internet Explorer',
            '/trident/i'   => 'Internet Explorer',
            '/firefox/i'   => 'Firefox',
            '/safari/i'    => 'Safari',
            '/chrome/i'    => 'Chrome',
            '/edge/i'      => 'Edge',
            '/edg/i'       => 'Edge',
            '/opera/i'     => 'Opera',
            '/netscape/i'  => 'Netscape',
            '/maxthon/i'   => 'Maxthon',
            '/konqueror/i' => 'Konqueror'
        ];

        foreach ($browserArray as $regex => $value) {
            if (preg_match($regex, $httpUserAgent)) {
                $browser = $value;
            }
        }

        return $browser;
    }

    public function navigate(): array
    {
        $httpUserAgent = $_SERVER['HTTP_USER_AGENT'];
        $navigate = 'Inconnu';

        $osArray = [
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        ];

        foreach ($osArray as $regex => $value) {
            if (preg_match($regex, $httpUserAgent)) {
                $navigate = $value;
            }
        }

        $osDevice = 'PC';

        if (
            $navigate === 'iPhone'
            || $navigate === 'iPod'
            || $navigate === 'iPad'
            || $navigate === 'Android'
            || $navigate === 'Mobile'
        ) {
            $osDevice = 'Mobile';
        } elseif ($this->browser() === 'Inconnu' || $navigate === 'Inconnu') {
            $osDevice = 'Inconnu';
        }

        return [$navigate, $osDevice];
    }

    public function newIfUserConnected(
        Visitor $visitor,
        UserInterface $user,
        RequestEvent $event
    ): bool
    {
        $visitor->setUser($user);
        $visitor->setNumberVisit(1);
        $visitor->setConnected(true);

        $this->setInformationVisitor($visitor, $event);

        $this->visitorRepository->save($visitor);

        return true;
    }

    public function updateIfUserConnected(
        Visitor $visitor,
        RequestEvent $event
    ): bool
    {
        $visitor->setNumberVisit($visitor->getNumberVisit()+1);
        $visitor->setConnected(true);

        $this->setInformationVisitor($visitor, $event);

        $this->visitorRepository->update($visitor);

        return true;
    }

    public function updateIfIpExistAndUserDisconnect(
        Visitor $visitor,
        RequestEvent $event
    ): bool
    {
        if ($event->getRequest()->get('_route') === self::ROUTE_DEV_BAR_SYMFONY) {
            return true;
        }

        $visitor->setNumberVisit($visitor->getNumberVisit()+1);
        $visitor->setConnected(false);

        $this->setInformationVisitor($visitor, $event);

        $this->visitorRepository->update($visitor);

        return true;
    }

    public function updateIfIpNotExist(
        Visitor $visitor,
        RequestEvent $event
    ): bool
    {
        $this->setInformationVisitor($visitor, $event);

        $visitor->setNumberVisit(1);
        $visitor->setConnected(false);

        $this->visitorRepository->save($visitor);

        return true;
    }

    protected function setInformationVisitor(
        Visitor $visitor,
        RequestEvent $event
    ): bool
    {
        $visitor->setIp($_SERVER['REMOTE_ADDR']);
        $visitor->setLastVisitAt(new DateTimeImmutable());
        $visitor->setRouteName($event->getRequest()->get('_route'));
        $visitor->setNavigator($this->browser());
        $visitor->setPlatform($this->navigate()[0]);
        $visitor->setDeviceType($this->navigate()[1]);

        return true;
    }
}
