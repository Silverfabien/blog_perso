<?php

namespace App\EventListener;

use App\Entity\Article\Article;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class PictureCacheSubscriber implements EventSubscriber
{
    private $cacheManager;
    private $uploaderHelper;

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
    public function getSubscribedEvents()
    {
        return ['preUpdate'];
    }

    public function preUpdate(
        LifecycleEventArgs $args
    )
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Article) {
            return;
        }

        if ($entity->getPictureFile() instanceof UploadedFile) {
            $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'pictureFile'));
        }
    }
}
