<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ModificationUtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UtilisateurController extends AbstractController
{
    #[Route('/mon-profil/modifier', name: 'app_utilisateur_modifier', methods: ['GET', 'POST'])]
    public function modifier(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $utilisateur = $this->getUser();

        if (!$utilisateur instanceof Utilisateur) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        $formulaire = $this->createForm(ModificationUtilisateurType::class, $utilisateur);
        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $fichierPhoto = $formulaire->get('photoFichier')->getData();

            if ($fichierPhoto instanceof UploadedFile) {
                $extension = $fichierPhoto->guessExtension();
                $nomPhoto = bin2hex(random_bytes(16)).'.'.$extension;
                $dossier = (string) $this->getParameter('kernel.project_dir').'/public/uploads';

                $fichierPhoto->move($dossier, $nomPhoto);
                $utilisateur->setPhoto($nomPhoto);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été modifié.');

            return $this->redirectToRoute('app_utilisateur_modifier');
        }

        return $this->render('utilisateur/modification_utilisateur.html.twig', [
            'formulaireUtilisateur' => $formulaire,
            'utilisateur' => $utilisateur,
        ]);
    }
}
