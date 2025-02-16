<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FermiersController extends AbstractController
{
    #[Route('/fermiers', name: 'app_fermiers')]
    public function index(): Response
    {
        return $this->render('fermiers/index.html.twig', [
            'controller_name' => 'FermiersController',
        ]);
    }
}
