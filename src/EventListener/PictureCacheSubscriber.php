<?php

namespace App\EventListener;

use App\Entity\Article\Article;
use App\Entity\User\UserPicture;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class PictureCacheSubscriber implements EventSubscriber
{
    private CacheManager $cacheManager;
    private UploaderHelper $uploaderHelper;

    public function __construct(
        CacheManager $cacheManager,
        UploaderHelper $uploaderHelper
    )
    {
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array
    {
        return ['preRemove', 'preUpdate'];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(
        LifecycleEventArgs $args
    ): void
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Article || !$entity instanceof UserPicture) {
            return;
        }

        $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'pictureFile'));
    }

    public function preUpdate(
        LifecycleEventArgs $args
    ): void
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Article || !$entity instanceof UserPicture) {
            return;
        }

        if ($entity->getPictureFile() instanceof UploadedFile) {
            $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'pictureFile'));
        }
    }
}
