<?php

namespace App\Entity\User;

use App\Repository\User\BlockedRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BlockedRepository::class)
 */
class Blocked
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $blocked;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $blockedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $blockedReason;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $unblockedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $unblockedReason;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="blockeds")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBlocked(): ?bool
    {
        return $this->blocked;
    }

    public function setBlocked(bool $blocked): self
    {
        $this->blocked = $blocked;

        return $this;
    }

    public function getBlockedAt(): ?\DateTimeImmutable
    {
        return $this->blockedAt;
    }

    public function setBlockedAt(?\DateTimeImmutable $blockedAt): self
    {
        $this->blockedAt = $blockedAt;

        return $this;
    }

    public function getBlockedReason(): ?string
    {
        return $this->blockedReason;
    }

    public function setBlockedReason(string $blockedReason): self
    {
        $this->blockedReason = $blockedReason;

        return $this;
    }

    public function getUnblockedAt(): ?\DateTimeImmutable
    {
        return $this->unblockedAt;
    }

    public function setUnblockedAt(?\DateTimeImmutable $unblockedAt): self
    {
        $this->unblockedAt = $unblockedAt;

        return $this;
    }

    public function getUnblockedReason(): ?string
    {
        return $this->unblockedReason;
    }

    public function setUnblockedReason(?string $unblockedReason): self
    {
        $this->unblockedReason = $unblockedReason;

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
}
