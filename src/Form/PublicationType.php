<?php

namespace App\Form;

use App\Entity\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PublicationType extends AbstractType
{
    /**
     * Construction du formulaire de publication en français
     * Conforme aux spécifications : Sans le champ utilisateur (géré en arrière-plan)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ de texte pour le contenu du message
            ->add('contenu', TextareaType::class, [
                'required' => false,
                'label' => 'Contenu du message',
                'attr' => [
                    'placeholder' => 'Écrivez votre message pour vos voisins ici...',
                    'rows' => 4
                ]
            ])
            // Menu déroulant pour le choix exclusif de la visibilité exigé par le cahier des charges
            ->add('visibilite', ChoiceType::class, [
                'label' => 'Qui peut voir cette publication ?',
                'choices' => [
                    'Tout le monde (Publique)' => 'public',
                    'Mes amis uniquement' => 'amis',
                ],
                'expanded' => false, // Menu déroulant classique
                'multiple' => false, // Choix unique obligatoire
            ])
            // Champ d'upload d'image découplé de la base de données (mapped false) pour éviter les conflits de types
            ->add('photoFichier', FileType::class, [
                'label' => 'Ajouter ou modifier la photo (Optionnel)',
                'required' => false,
                'mapped' => false, // Dit à Symfony de ne pas chercher à l'associer automatiquement au texte
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Choisissez une image JPG ou PNG de 2 Mo maximum.',
                    ]),
                ],
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
