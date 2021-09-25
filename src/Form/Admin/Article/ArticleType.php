<?php

namespace App\Form\Admin\Article;

use App\Entity\Article\Article;
use App\Entity\Article\Tags;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Description',
                'config' => [
                    'toolbar' => 'article_description_config',
                    'wordcount' => [
                        'maxCharCount' => 2000,
                        'showCharCount' => true
                    ]
                ]
            ])
            ->add('pictureFile', FileType::class, [
                'label' => 'Image',
                'required' => false
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Contenu',
                'config' => [
                    'toolbar' => 'article_content_config',
                    'wordcount' => [
                        'showCharCount' => true
                    ]
                ]
            ])
            ->add('tags', EntityType::class, [
                'class' => Tags::class,
                'choice_label' => 'name',
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
