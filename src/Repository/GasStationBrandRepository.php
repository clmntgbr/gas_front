<?php

namespace App\Repository;

use App\Entity\GasStationBrand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GasStationBrand>
 *
 * @method GasStationBrand|null find($id, $lockMode = null, $lockVersion = null)
 * @method GasStationBrand|null findOneBy(array $criteria, array $orderBy = null)
 * @method GasStationBrand[]    findAll()
 * @method GasStationBrand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GasStationBrandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GasStationBrand::class);
    }

//    /**
//     * @return GasStationBrand[] Returns an array of GasStationBrand objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GasStationBrand
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
