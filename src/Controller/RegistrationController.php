<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // GESTION DU TÉLÉCHARGEMENT DE LA PHOTO OBLIGATOIRE
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('photo')->getData();

            if ($imageFile) {
                // Créer un nom unique (ex: 64b73f82.jpg) pour éviter les doublons
                $nomUnique = uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer physiquement la photo dans le dossier public/uploads/ 
                $imageFile->move(
                    $this->getParameter('images_directory') . '/public/uploads',
                    $nomUnique
                );

                // Enregistrer le nom du fichier dans la case "photo" de l'utilisateur
                $user->setPhoto($nomUnique);
            }

            // HACHAGE DU MOT DE PASSE SECRET
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // ENREGISTREMENT AUTOMATIQUE DES AUTRES CHAMPS
            $user->setDateInscription(new \DateTimeImmutable());
            $user->setEstEnLigne(false); // L'utilisateur n'est pas encore connecté
            $user->setRoles(['ROLE_USER']); // Badge de membre standard obligatoire

            // Préparer l'insertion en base de données SQL
            $entityManager->persist($user);
            // Exécuter les requêtes et l'envoi des données
            $entityManager->flush(); // enregistre en bdd

            /* Faites tout ce dont vous avez besoin ici, comme envoyer un e-mail */

            // Message flash de succès
            // Messages de session spéciaux conçus pour un usage unique : ils disparaissent automatiquement de la session dès qu’ils sont récupérés. Ils sont donc idéaux pour stocker les notifications utilisateur.
            $this->addFlash('success', 'Votre compte a bien été créé ! Vous pouvez maintenant vous connecter.');

            // Redirection obligatoire vers la page de connexion
            return $this->redirectToRoute('app_connexion');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
