<?php

namespace App\ControllerHandler\Admin\Article;

use App\Entity\Article\Tags;
use App\Repository\Article\TagsRepository;
use Symfony\Component\Form\FormInterface;

/**
 * Class TagsHandler
 */
class TagsHandler
{
    private $tagsRepository;

    public function __construct(
        TagsRepository $tagsRepository
    )
    {
        $this->tagsRepository = $tagsRepository;
    }

    /**
     * @param FormInterface $form
     * @param Tags $tags
     * @return bool
     */
    public function createTagsHandle(
        FormInterface $form,
        Tags $tags
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagsRepository->save($tags);

            return true;
        }

        return false;
    }

    public function editTagsHandle(
        FormInterface $form,
        Tags $tags
    ): bool
    {
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagsRepository->update($tags);

            return true;
        }

        return false;
    }

    public function deleteTasHandle(
        Tags $tags
    ): bool
    {
        $this->tagsRepository->remove($tags);

        return true;
    }
}
