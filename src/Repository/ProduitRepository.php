<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }
    
    public function getTopSellingProducts(int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.nomprod as name, SUM(oi.quantite) as totalSold')
            ->join('p.orderItems', 'oi')
            ->groupBy('p.id')
            ->orderBy('totalSold', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getRevenueByProduct(): array
    {
        $results = $this->createQueryBuilder('p')
            ->select('p.nomprod as name, SUM(oi.prix * oi.quantite) as revenue')
            ->join('p.orderItems', 'oi')
            ->groupBy('p.id')
            ->orderBy('revenue', 'DESC')
            ->getQuery()
            ->getResult();

        // Ensure consistent array structure even if empty
        if (empty($results)) {
            return [['name' => 'No products', 'revenue' => 0]];
        }

        return $results;
    }

    public function getActiveProductsCount(): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.status = :status')
            ->setParameter('status', true)
            ->getQuery()
            ->getSingleScalarResult();
    }


    //    /**
    //     * @return Produit[] Returns an array of Produit objects
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

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
