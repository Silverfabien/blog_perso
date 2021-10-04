<?php

namespace App\Form\Admin\User;

use App\Entity\User\Rank;
use App\Entity\User\User;
use App\Form\User\UserPictureType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Prénom', 'autofocus' => true
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le prénom ne doit pas dépasser {{ limit }} caractères !'
                    ])
                ]])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom', 'autofocus' => true
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères !'
                    ])
                ]])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Votre email'
                ],
                'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Votre email ne doit pas dépasser {{ limit }} caractères !'
                    ])
                ]])
            ->add('rank', EntityType::class, [
                'class' => Rank::class,
                'label' => 'Grade',
                'choice_label' => 'rolename',
                'multiple' => false
            ])
            ->add('picture', UserPictureType::class, [
                'label' => false
            ])
            ->add('changePicture', CheckboxType::class, [
                'label' => "Ne pas modifier l'image de profil ?",
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'checked' => true
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
