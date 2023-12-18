<?php

namespace App\Repository;

use App\Entity\Advert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Array_;

/**
 * @extends ServiceEntityRepository<Advert>
 *
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{

    const PAGINATOR_PER_PAGE = 30;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advert::class);
    }
    public function getAdvertPaginator(int $offset): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($query);
    }

    public function deleteAdvert(Advert $advert)
    {
        $entityManager = $this->getEntityManager();

        // Supprimer l'entité
        $entityManager->remove($advert);

        // Appliquer les changements à la base de données
        $entityManager->flush();
    }

    public function getOldAdvert(\DateTimeImmutable $date): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.createdAt < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

    public function getOldPublishedAdvert(\DateTimeImmutable $date): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.publishedAt < :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Advert[] Returns an array of Advert objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Advert
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
