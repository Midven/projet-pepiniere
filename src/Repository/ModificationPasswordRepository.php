<?php

namespace App\Repository;

use App\Entity\ModificationPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ModificationPassword|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModificationPassword|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModificationPassword[]    findAll()
 * @method ModificationPassword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModificationPasswordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModificationPassword::class);
    }

    // /**
    //  * @return ModificationPassword[] Returns an array of ModificationPassword objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ModificationPassword
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
