<?php

namespace App\Form\Contact;

use App\Entity\Contact\Contact;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'autofocus' => true,
                    'placeholder' => 'Votre Nom'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'attr' => [
                    'autofocus' => true,
                    'placeholder' => 'Votre Prénom'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Votre Email'
                ]
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'Proposition' => 'Proposition',
                    'Demande de Renseignement' => 'Renseignement',
                    'Report d\'un bug' => 'Bug',
                    'Problème avec mon compte' => 'Compte',
                    'Mon compte est bloqué' => 'Compte bloqué',
                    'Je voudrais restituer mon compte' => 'Compte supprimé',
                    'Signaler une personne'=> 'Signalement',
                    'Autres' => 'Autres'
                ]
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Le sujet de votre demande'
                ]
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Contenu',
                'required' => true,
                'config' => [
                    'toolbar' => 'contact_config'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
