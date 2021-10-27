<?php

namespace App\Entity\Visitor;

use App\Entity\User\User;
use App\Repository\Visitor\VisitorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VisitorRepository::class)
 */
class Visitor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ip;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $firstVisitAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $lastVisitAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $routeName;

    /**
     * @ORM\Column(type="integer")
     */
    private $numberVisit;

    /**
     * @ORM\Column(type="boolean")
     */
    private $connected;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $navigator;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $platform;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $deviceType;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     */
    private $url;

    public function __construct()
    {
        $this->firstVisitAt = new \DateTimeImmutable();
        $this->connected = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getFirstVisitAt(): ?\DateTimeImmutable
    {
        return $this->firstVisitAt;
    }

    public function setFirstVisitAt(\DateTimeImmutable $firstVisitAt): self
    {
        $this->firstVisitAt = $firstVisitAt;

        return $this;
    }

    public function getLastVisitAt(): ?\DateTimeImmutable
    {
        return $this->lastVisitAt;
    }

    public function setLastVisitAt(\DateTimeImmutable $lastVisitAt): self
    {
        $this->lastVisitAt = $lastVisitAt;

        return $this;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function setRouteName(string $routeName): self
    {
        $this->routeName = $routeName;

        return $this;
    }

    public function getNumberVisit(): ?int
    {
        return $this->numberVisit;
    }

    public function setNumberVisit(int $numberVisit): self
    {
        $this->numberVisit = $numberVisit;

        return $this;
    }

    public function getConnected(): ?bool
    {
        return $this->connected;
    }

    public function setConnected(bool $connected): self
    {
        $this->connected = $connected;

        return $this;
    }

    public function getNavigator(): ?string
    {
        return $this->navigator;
    }

    public function setNavigator(string $navigator): self
    {
        $this->navigator = $navigator;

        return $this;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function setPlatform(string $platform): self
    {
        $this->platform = $platform;

        return $this;
    }

    public function getDeviceType(): ?string
    {
        return $this->deviceType;
    }

    public function setDeviceType(string $deviceType): self
    {
        $this->deviceType = $deviceType;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
