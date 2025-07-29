<?php

namespace App\Repository;

use App\Entity\Moviemon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Moviemon>
 *
 * @method Moviemon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Moviemon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Moviemon[]    findAll()
 * @method Moviemon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoviemonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Moviemon::class);
    }

//    /**
//     * @return Moviemon[] Returns an array of Moviemon objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Moviemon
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
