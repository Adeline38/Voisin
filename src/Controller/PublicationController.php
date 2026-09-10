<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Entity\Utilisateur;
use App\Form\PublicationType;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PublicationController extends AbstractController
{
    #[Route('/fil-actualite', name: 'app_fil_actualite', methods: ['GET', 'POST'])]
    public function filActualite(
        Request $request,
        PublicationRepository $publicationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $utilisateur = $this->getUser();

        if (!$utilisateur instanceof Utilisateur) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        $nouvellePublication = new Publication();
        $formulaire = $this->createForm(PublicationType::class, $nouvellePublication);
        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted()) {
            $contenu = $nouvellePublication->getContenu();
            $fichierPhoto = $formulaire->get('photoFichier')->getData();

            $contenuEstVide = $contenu === null;
            if ($contenu !== null) {
                $contenuEstVide = trim($contenu) === '';
            }

            if ($contenuEstVide && $fichierPhoto === null) {
                $formulaire->addError(
                    new FormError('Ajoutez un texte, une image, ou les deux.')
                );
            }
        }

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $nouvellePublication->setUtilisateur($utilisateur);
            $nouvellePublication->setDateCreation(new \DateTimeImmutable());

            $fichierPhoto = $formulaire->get('photoFichier')->getData();
            if ($fichierPhoto instanceof UploadedFile) {
                $nomPhoto = $this->enregistrerPhoto($fichierPhoto);
                $nouvellePublication->setPhoto($nomPhoto);
            }

            $entityManager->persist($nouvellePublication);
            $entityManager->flush();

            $this->addFlash('success', 'Votre publication a bien été partagée.');

            return $this->redirectToRoute('app_fil_actualite');
        }

        return $this->render('publication/accueil_publications_privees.html.twig', [
            'publications' => $publicationRepository->findVisiblesSansAmitie($utilisateur),
            'formulairePublication' => $formulaire,
        ]);
    }

    #[Route('/publication/{id}', name: 'app_publication_detail', methods: ['GET'])]
    public function detail(Publication $publication): Response
    {
        $utilisateur = $this->getUser();
        $estAuteur = $utilisateur instanceof Utilisateur
            && $publication->getUtilisateur() === $utilisateur;

        if ($publication->getVisibilite() !== 'public' && !$estAuteur) {
            throw $this->createAccessDeniedException(
                'Cette publication est réservée à son auteur et à ses amis.'
            );
        }

        return $this->render('publication/commentaires_publication.html.twig', [
            'publication' => $publication,
        ]);
    }

    #[Route('/publication/{id}/modifier', name: 'app_publication_modifier', methods: ['GET', 'POST'])]
    public function modifier(
        Request $request,
        Publication $publication,
        EntityManagerInterface $entityManager
    ): Response {
        if ($publication->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException(
                'Vous ne pouvez pas modifier la publication d’un autre utilisateur.'
            );
        }

        $formulaire = $this->createForm(PublicationType::class, $publication);
        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted()) {
            $contenu = $publication->getContenu();
            $nouvellePhoto = $formulaire->get('photoFichier')->getData();
            $supprimerPhoto = $request->getPayload()->getString('supprimer_photo') === '1';

            $contenuEstVide = $contenu === null;
            if ($contenu !== null) {
                $contenuEstVide = trim($contenu) === '';
            }

            $photoSeraPresente = $publication->getPhoto() !== null && !$supprimerPhoto;
            if ($nouvellePhoto instanceof UploadedFile) {
                $photoSeraPresente = true;
            }

            if ($contenuEstVide && !$photoSeraPresente) {
                $formulaire->addError(
                    new FormError('Conservez au moins un texte ou une image.')
                );
            }
        }

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $nouvellePhoto = $formulaire->get('photoFichier')->getData();
            $supprimerPhoto = $request->getPayload()->getString('supprimer_photo') === '1';

            if ($supprimerPhoto || $nouvellePhoto instanceof UploadedFile) {
                $this->supprimerPhoto($publication);
                $publication->setPhoto(null);
            }

            if ($nouvellePhoto instanceof UploadedFile) {
                $nomPhoto = $this->enregistrerPhoto($nouvellePhoto);
                $publication->setPhoto($nomPhoto);
            }

            $publication->setDateModification(new \DateTime());
            $entityManager->flush();

            $this->addFlash('success', 'Votre publication a été modifiée.');

            return $this->redirectToRoute('app_fil_actualite');
        }

        return $this->render('publication/modification_publication.html.twig', [
            'publication' => $publication,
            'formulairePublication' => $formulaire,
        ]);
    }

    #[Route('/publication/{id}/supprimer', name: 'app_publication_supprimer', methods: ['POST'])]
    public function supprimer(
        Request $request,
        Publication $publication,
        EntityManagerInterface $entityManager
    ): Response {
        if ($publication->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException(
                'Vous ne pouvez pas supprimer la publication d’un autre utilisateur.'
            );
        }

        $jeton = $request->getPayload()->getString('_token');
        $identifiantJeton = 'supprimer_publication_'.$publication->getId();

        if (!$this->isCsrfTokenValid($identifiantJeton, $jeton)) {
            throw $this->createAccessDeniedException('Jeton de sécurité invalide.');
        }

        $this->supprimerPhoto($publication);
        $entityManager->remove($publication);
        $entityManager->flush();

        $this->addFlash('success', 'La publication a été supprimée.');

        return $this->redirectToRoute('app_fil_actualite');
    }

    private function enregistrerPhoto(UploadedFile $fichierPhoto): string
    {
        $extension = $fichierPhoto->guessExtension();
        $nomPhoto = bin2hex(random_bytes(16)).'.'.$extension;
        $dossier = (string) $this->getParameter('kernel.project_dir').'/public/uploads';

        $fichierPhoto->move($dossier, $nomPhoto);

        return $nomPhoto;
    }

    private function supprimerPhoto(Publication $publication): void
    {
        $nomPhoto = $publication->getPhoto();

        if ($nomPhoto === null) {
            return;
        }

        $cheminPhoto = (string) $this->getParameter('kernel.project_dir')
            .'/public/uploads/'.$nomPhoto;

        if (is_file($cheminPhoto)) {
            unlink($cheminPhoto);
        }
    }
}
