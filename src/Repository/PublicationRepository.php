<?php

namespace App\Repository;

use App\Entity\Publication;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Publication>
 */
class PublicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Publication::class);
    }

    /**
     * Retourne les publications visibles avant la gestion des amitiés :
     * les publications publiques et toutes celles du membre connecté.
     *
     * @return Publication[]
     */
    public function findVisiblesSansAmitie(Utilisateur $utilisateur): array
    {
        return $this->createQueryBuilder('publication')
            ->where('publication.visibilite = :visibilitePublique')
            ->orWhere('publication.utilisateur = :utilisateur')
            ->setParameter('visibilitePublique', 'public')
            ->setParameter('utilisateur', $utilisateur)
            ->orderBy('publication.date_creation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne au maximum six publications publiques récentes.
     *
     * @return Publication[]
     */
    public function findPubliquesRecentes(int $limite = 6): array
    {
        return $this->createQueryBuilder('publication')
            ->where('publication.visibilite = :visibilitePublique')
            ->setParameter('visibilitePublique', 'public')
            ->orderBy('publication.date_creation', 'DESC')
            ->setMaxResults($limite)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Publication[] Returns an array of Publication objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Publication
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
