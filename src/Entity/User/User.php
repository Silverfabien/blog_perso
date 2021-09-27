<?php

namespace App\Entity\User;

use App\Entity\Article\Comment;
use App\Repository\User\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $lastname;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ip;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $lastConnectedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $confirmationAccount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $confirmationAccountToken;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $confirmationAccountAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetToken;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $resetTokenCreatedAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $resetTokenExpiredAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $resetLastAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deleted;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $undeletedAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $connectionAttempt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $connectionAttemptExpiredAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $connectionAttemptDef;

    /**
     * @ORM\ManyToOne(targetEntity=Rank::class, inversedBy="users")
     */
    private $rank;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User\UserPicture", mappedBy="user", cascade={"persist"})
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity=Blocked::class, mappedBy="user")
     */
    private $blockeds;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->confirmationAccount = false;
        $this->deleted = false;
        $this->connectionAttempt = 0;
        $this->connectionAttemptDef = false;
        $this->blockeds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->rank->getRole();
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getLastConnectedAt(): ?\DateTimeImmutable
    {
        return $this->lastConnectedAt;
    }

    public function setLastConnectedAt(?\DateTimeImmutable $lastConnectedAt): self
    {
        $this->lastConnectedAt = $lastConnectedAt;

        return $this;
    }

    public function getConfirmationAccount(): ?bool
    {
        return $this->confirmationAccount;
    }

    public function setConfirmationAccount(bool $confirmationAccount): self
    {
        $this->confirmationAccount = $confirmationAccount;

        return $this;
    }

    public function getConfirmationAccountToken(): ?string
    {
        return $this->confirmationAccountToken;
    }

    public function setConfirmationAccountToken(?string $confirmationAccountToken): self
    {
        $this->confirmationAccountToken = $confirmationAccountToken;

        return $this;
    }

    public function getConfirmationAccountAt(): ?\DateTimeImmutable
    {
        return $this->confirmationAccountAt;
    }

    public function setConfirmationAccountAt(?\DateTimeImmutable $confirmationAccountAt): self
    {
        $this->confirmationAccountAt = $confirmationAccountAt;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getResetTokenCreatedAt(): ?\DateTimeImmutable
    {
        return $this->resetTokenCreatedAt;
    }

    public function setResetTokenCreatedAt(?\DateTimeImmutable $resetTokenCreatedAt): self
    {
        $this->resetTokenCreatedAt = $resetTokenCreatedAt;

        return $this;
    }

    public function getResetTokenExpiredAt(): ?\DateTimeImmutable
    {
        return $this->resetTokenExpiredAt;
    }

    public function setResetTokenExpiredAt(?\DateTimeImmutable $resetTokenExpiredAt): self
    {
        $this->resetTokenExpiredAt = $resetTokenExpiredAt;

        return $this;
    }

    public function getResetLastAt(): ?\DateTimeImmutable
    {
        return $this->resetLastAt;
    }

    public function setResetLastAt(?\DateTimeImmutable $resetLastAt): self
    {
        $this->resetLastAt = $resetLastAt;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getUndeletedAt(): ?\DateTimeImmutable
    {
        return $this->undeletedAt;
    }

    public function setUndeletedAt(?\DateTimeImmutable $undeletedAt): self
    {
        $this->undeletedAt = $undeletedAt;

        return $this;
    }

    public function getConnectionAttempt(): ?int
    {
        return $this->connectionAttempt;
    }

    public function setConnectionAttempt(int $connectionAttempt): self
    {
        $this->connectionAttempt = $connectionAttempt;

        return $this;
    }

    public function getConnectionAttemptExpiredAt(): ?\DateTimeImmutable
    {
        return $this->connectionAttemptExpiredAt;
    }

    public function setConnectionAttemptExpiredAt(?\DateTimeImmutable $connectionAttemptExpiredAt): self
    {
        $this->connectionAttemptExpiredAt = $connectionAttemptExpiredAt;

        return $this;
    }

    public function getConnectionAttemptDef(): ?bool
    {
        return $this->connectionAttemptDef;
    }

    public function setConnectionAttemptDef(bool $connectionAttemptDef): self
    {
        $this->connectionAttemptDef = $connectionAttemptDef;

        return $this;
    }

    public function getRank(): ?Rank
    {
        return $this->rank;
    }

    public function setRank(?Rank $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * @return UserPicture|null
     */
    public function getPicture(): ?UserPicture
    {
        return $this->picture;
    }

    /**
     * @param $picture
     * @return \App\Entity\User\User
     */
    public function setPicture($picture): self
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * @return Collection|Blocked[]
     */
    public function getBlockeds(): Collection
    {
        return $this->blockeds;
    }

    public function addBlocked(Blocked $blocked): self
    {
        if (!$this->blockeds->contains($blocked)) {
            $this->blockeds[] = $blocked;
            $blocked->setUser($this);
        }

        return $this;
    }

    public function removeBlocked(Blocked $blocked): self
    {
        if ($this->blockeds->removeElement($blocked)) {
            // set the owning side to null (unless already changed)
            if ($blocked->getUser() === $this) {
                $blocked->setUser(null);
            }
        }

        return $this;
    }
}
