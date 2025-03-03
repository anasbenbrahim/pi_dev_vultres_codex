<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;
use App\Repository\ProduitRepository;

class MetricsController extends AbstractController
{
    #[Route('/metrics/sales', name: 'app_metrics_sales')]
    public function salesDashboard(OrderRepository $orderRepository): Response
    {
        // Get sales metrics
        $totalSales = $orderRepository->getTotalSales();
        $avgOrderValue = $orderRepository->getAverageOrderValue();
        $orderStatusDistribution = $orderRepository->getOrderStatusDistribution();

        return $this->render('metrics/sales.html.twig', [
            'totalSales' => $totalSales,
            'avgOrderValue' => $avgOrderValue,
            'orderStatusDistribution' => $orderStatusDistribution,
        ]);
    }

    #[Route('/metrics/products', name: 'app_metrics_products')]
    public function productPerformance(ProduitRepository $produitRepository): Response
    {
        // Get product metrics
        $topSellingProducts = $produitRepository->getTopSellingProducts();
        $revenueByProduct = $produitRepository->getRevenueByProduct();
        $activeProductsCount = $produitRepository->getActiveProductsCount();

        return $this->render('metrics/products.html.twig', [
            'topSellingProducts' => $topSellingProducts,
            'revenueByProduct' => $revenueByProduct,
            'activeProductsCount' => $activeProductsCount,
        ]);
    }
}
