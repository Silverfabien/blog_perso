<?php

namespace App\Form\Admin\User;

use App\Entity\User\Blocked;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlockedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('blockedReason', TextType::class, [
                'label' => 'Raison du blocage'
            ])
            ->add('time', TextType::class, [
                'label' => 'Temps en minute',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Définitif si null ou int minute|hour|day|month|year'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Blocked::class,
        ]);
    }
}
