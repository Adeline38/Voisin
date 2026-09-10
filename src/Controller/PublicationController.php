<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Form\PublicationType;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/publication')]
final class PublicationController extends AbstractController
{
    #[Route(name: 'app_publication_index', methods: ['GET'])]
    public function index(PublicationRepository $publicationRepository): Response
    {
        return $this->render('publication/index.html.twig', [
            'publications' => $publicationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_publication_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $publication = new Publication();
        $form = $this->createForm(PublicationType::class, $publication);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // SÉCURITÉ : On attribue de force l'auteur connecté sans laisser le choix au formulaire
            $publication->setUtilisateur($this->getUser());
            $publication->setDateCreation(new \DateTimeImmutable());

            $imageFile = $form->get('image_upload')->getData();
            if ($imageFile) {
                $nomUnique = uniqid() . '.' . $imageFile->guessExtension();
                $cheminDossierPublic = $this->getParameter('kernel.project_dir') . '/public/uploads';
                $imageFile->move($cheminDossierPublic, $nomUnique);
                $publication->setPhoto($nomUnique);
            }

            $entityManager->persist($publication);
            $entityManager->flush();

            $this->addFlash('success', 'Votre publication a bien été partagée !');
            return $this->redirectToRoute('app_publication_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('publication/new.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_publication_show', methods: ['GET'])]
    public function show(Publication $publication): Response
    {
        return $this->render('publication/show.html.twig', [
            'publication' => $publication,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_publication_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
    {
        // ❌ SÉCURITÉ PIRATAGE URL : Si le connecté n'est pas l'auteur, on bloque immédiatement (Erreur 403 Access Denied)
        if ($publication->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Sécurité Examen : Vous n'avez pas le droit de modifier la publication d'un autre utilisateur !");
        }

        $form = $this->createForm(PublicationType::class, $publication);

        if ($request->isMethod('POST')) {
            $donneesFormulaire = $request->request->all('publication');
            $texteBrut = $donneesFormulaire['contenu'] ?? '';
            $veutSupprimerImage = $request->request->get('supprimer_image_brute') === '1';
            
            $fichiersDonnees = $request->files->all('publication');
            $nouvelleImage = $fichiersDonnees['image_upload'] ?? null;

            // CALCUL DE LA PHOTO FINALE
            $auraUnePhotoApresSoumission = false;
            if ($publication->getPhoto() !== null && $veutSupprimerImage === false) {
                $auraUnePhotoApresSoumission = true;
            }
            if ($nouvelleImage !== null) {
                $auraUnePhotoApresSoumission = true;
            }

            // BARRIÈRE DU CAHIER DES CHARGES : TOUT VIDE INTERDIT
            if (empty(trim($texteBrut)) && $auraUnePhotoApresSoumission === false) {
                return $this->render('publication/edit.html.twig', [
                    'publication' => $publication,
                    'form' => $form->createView(),
                    'message_erreur' => 'Sécurité Examen : Une publication doit obligatoirement contenir du texte ou une image. Vous ne pouvez pas vider entièrement ce message.'
                ]);
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $publication->setDateModification(new \DateTime());

            $veutSupprimerFinal = $request->request->get('supprimer_image_brute') === '1';
            $nouvelleImageFinale = $form->get('image_upload')->getData();

            if ($veutSupprimerFinal === true) {
                if ($publication->getPhoto()) {
                    $cheminFichierPhysique = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $publication->getPhoto();
                    if (file_exists($cheminFichierPhysique)) {
                        unlink($cheminFichierPhysique);
                    }
                    $publication->setPhoto(null);
                }
            }

            if ($nouvelleImageFinale) {
                $nomUnique = uniqid() . '.' . $nouvelleImageFinale->guessExtension();
                $nouvelleImageFinale->move($this->getParameter('kernel.project_dir') . '/public/uploads', $nomUnique);
                $publication->setPhoto($nomUnique);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Votre publication a été modifiée avec succès.');
            return $this->redirectToRoute('app_publication_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('publication/edit.html.twig', [
            'publication' => $publication,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_publication_delete', methods: ['POST'])]
    public function delete(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
    {
        // ❌ SÉCURITÉ PIRATAGE URL : Si le connecté n'est pas l'auteur, interdiction d'effacer
        if ($publication->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Sécurité Examen : Vous n'êtes pas le propriétaire de ce message !");
        }

        // ❌ SÉCURITÉ JETON CSRF : On valide le badge secret inclus dans le bouton pour prouver que l'ordre vient bien de notre site [8]
        $tokenID = 'delete' . $publication->getId();
        $tokenValeur = $request->getPayload()->getString('_token');
        
        if ($this->isCsrfTokenValid($tokenID, $tokenValeur)) {
            // Nettoyage de la photo dans le disque dur avant de détruire la ligne SQL
            if ($publication->getPhoto()) {
                $cheminFichierPhysique = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $publication->getPhoto();
                if (file_exists($cheminFichierPhysique)) {
                    unlink($cheminFichierPhysique);
                }
            }

            $entityManager->remove($publication);
            $entityManager->flush();
            $this->addFlash('success', 'La publication a été définitivement supprimée.');
        }

        return $this->redirectToRoute('app_publication_index', [], Response::HTTP_SEE_OTHER);
    }
}
