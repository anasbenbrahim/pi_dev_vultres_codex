<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\ProduitRepository; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    private $orderRepository;
    private $produitRepository; 

    public function __construct(OrderRepository $orderRepository, ProduitRepository $produitRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->produitRepository = $produitRepository; 
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        $totalSales = $this->orderRepository->getTotalSales();
        $avgOrderValue = $this->orderRepository->getAverageOrderValue();
        $orderStatusDistribution = $this->orderRepository->getOrderStatusDistribution();
        $activeProductsCount = $this->produitRepository->getActiveProductsCount(); 

        $topSellingProducts = $this->produitRepository->getTopSellingProducts(); 

        $revenueByProduct = $this->produitRepository->getRevenueByProduct(); 

        return $this->render('profile/index.html.twig', [
            'revenueByProduct' => $revenueByProduct,
            'topSellingProducts' => $topSellingProducts,
            'activeProductsCount' => $activeProductsCount,
            'totalSales' => $totalSales,
            'avgOrderValue' => $avgOrderValue,
            'orderStatusDistribution' => $orderStatusDistribution,
            'controller_name' => 'ProfileController',
            'user' => $user,
        ]);
    }
}
