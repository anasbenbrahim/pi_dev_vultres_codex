<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function getTotalSales(): float
    {
        return $this->createQueryBuilder('o')
            ->select('SUM(o.total) as totalSales')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function getAverageOrderValue(): float
    {
        return $this->createQueryBuilder('o')
            ->select('AVG(o.total) as avgOrderValue')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function getOrderStatusDistribution(): array
    {
        $results = $this->createQueryBuilder('o')
            ->select('o.status, COUNT(o.id) as count')
            ->groupBy('o.status')
            ->getQuery()
            ->getResult();

        // Ensure consistent array structure even if empty
        if (empty($results)) {
            return [['status' => 'pending', 'count' => 0]];
        }

        return $results;
    }
    public function save(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findUserOrders(int $userId): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('o.created_at', 'ASC')
            ->getQuery()
            ->getResult() ?: []; // Return an empty array if no results found
    }

    public function getRevenueByProductForUser(int $userId): array
    {
        return $this->createQueryBuilder('o')
            ->select('oi.produit, SUM(oi.prix * oi.quantity) as totalRevenue') // Assuming 'quantity' is the field for the number of products purchased
            ->join('o.orderItems', 'oi')
            ->where('o.user = :userId')
            ->setParameter('userId', $userId)
            ->groupBy('oi.produit')
            ->getQuery()
            ->getResult();
    }

      
}
