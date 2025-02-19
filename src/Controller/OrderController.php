<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/history', name: 'app_order_history', methods: ['GET'])]
    public function history(OrderRepository $orderRepository): Response
    {
        // Get the authenticated user
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $orders = $orderRepository->findUserOrders($user->getId());

        return $this->render('order/history.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/{id}', name: 'app_order_details', methods: ['GET'])]
    public function details(Order $order): Response
    {
        // Verify the order belongs to the logged-in user
        $user = $this->getUser();
        if ($order->getUser()->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('You do not have access to this order');
        }

        return $this->render('order/details.html.twig', [
            'order' => $order,
        ]);
    }
}
