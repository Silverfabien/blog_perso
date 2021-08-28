<?php

namespace App\Form\User;

use App\Entity\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ResetForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Votre email'
                ], 'constraints' => [
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'Votre email ne doit pas dépasser {{ limit }} caractères !'
                    ])
                ]])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passes doivent correspondre !',
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Tapez votre mot de passe'
                    ],
                    'constraints' => [
                        new Length([
                            'min' => 8,
                            'max' => 255,
                            'minMessage' => 'Votre mot de passe doit contenir plus de {{ limit }} caractères',
                            'maxMessage' => 'Votre mot de passe doit contenir moins de {{ limit }} caractères'
                        ])
                    ]
                ],
                'second_options' => [
                    'label' => 'Tapez le mot de passe à nouveau',
                    'attr' => [
                        'placeholder' => 'Tapez à nouveau votre mot de passe'
                    ],
                    'constraints' => [
                        new Length([
                            'min' => 8,
                            'max' => 255,
                            'minMessage' => 'Votre mot de passe doit contenir plus de {{ limit }} caractères',
                            'maxMessage' => 'Votre mot de passe doit contenir moins de {{ limit }} caractères'
                        ])
                    ]
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
