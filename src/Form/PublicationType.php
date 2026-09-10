<?php

namespace App\Form;

use App\Entity\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu', TextareaType::class, [
                'required' => false,
                'label' => 'Contenu du message',
                'attr' => ['placeholder' => 'Écrivez votre message ici...']
            ])
            ->add('visibilite', ChoiceType::class, [
                'label' => 'Visibilité',
                'choices' => [
                    'Publique (Tout le monde)' => 'public',
                    'Privée (Amis uniquement)' => 'friends',
                ],
            ])
            ->add('image_upload', FileType::class, [
                'label' => 'Changer la photo (Optionnel)',
                'required' => false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}