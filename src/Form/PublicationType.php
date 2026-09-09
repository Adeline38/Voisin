<?php

namespace App\Form;

use App\Entity\Publication;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu')
            ->add('photo', FileType::class, [
                'label' => 'Ajouter une photo à votre message (Optionnel)',
                'required' => false, // Rend le champ facultatif
                'mapped' => false,   // Dit à Symfony de ne pas chercher à l'enregistrer tout seul en texte
                'constraints' => [
                    new File([
                        'maxSize' => '500k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (Max 500 Ko, JPG, PNG ou WEBP).',
                    ])
                ],
            ])
            ->add('visibilite')            
            // 'date_creation' et 'date_modification' sont gérés automatiquement en arrière-plan par le contrôleur
            /* 
                ->add('date_creation', null, [
                    'widget' => 'single_text'
                ])
                ->add('date_modification')
            */
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'id',
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
