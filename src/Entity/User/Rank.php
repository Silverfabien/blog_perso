<?php

namespace App\Entity\User;

use App\Entity\User\User;
use App\Repository\User\RankRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RankRepository::class)
 */
class Rank
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
    private $rolename;

    /**
     * @ORM\Column(type="json")
     */
    private $role = [];

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="rank")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRolename(): ?string
    {
        return $this->rolename;
    }

    public function setRolename(string $rolename): self
    {
        $this->rolename = $rolename;

        return $this;
    }

    public function getRole(): ?array
    {
        return $this->role;
    }

    public function setRole(array $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setRank($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRank() === $this) {
                $user->setRank(null);
            }
        }

        return $this;
    }
}
