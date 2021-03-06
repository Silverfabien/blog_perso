<?php

namespace App\Entity\User;

use App\Repository\User\UserPictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserPictureRepository::class)
 *
 * @Vich\Uploadable()
 */
class UserPicture implements Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var File|null
     * @Assert\Image(mimeTypes={"image/jpeg", "image/png"}, mimeTypesMessage="Seule les images en .jpg et .png sont acceptées")
     * @Vich\UploadableField(mapping="user_picture", fileNameProperty="filename")
     */
    private $pictureFile;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"}, inversedBy="picture")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     * @return $this
     */
    public function setFilename(?string $filename): UserPicture
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }

    /**
     * @param File|null $pictureFile
     * @return $this
     */
    public function setPictureFile(?File $pictureFile): UserPicture
    {
        $this->pictureFile = $pictureFile;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string|void
     */
    public function serialize(): void
    {
        // TODO: Implement serialize() method.
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        // TODO: Implement unserialize() method.
    }
}
