<?php

namespace App\Repository;

use App\Entity\Tchats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Tchats|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tchats|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tchats[]    findAll()
 * @method Tchats[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TchatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tchats::class);
    }

    // /**
    //  * @return Tchats[] Returns an array of Tchats objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tchats
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
