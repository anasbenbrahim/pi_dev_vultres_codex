<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FournisseursController extends AbstractController
{
    #[Route('/fournisseurs', name: 'app_fournisseurs')]
    public function index(): Response
    {
        return $this->render('fournisseurs/index.html.twig', [
            'controller_name' => 'FournisseursController',
        ]);
    }
}
