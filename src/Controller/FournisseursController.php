<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FournisseursController extends AbstractController
{
    #[Route('/fournisseurs', name: 'app_fournisseurs')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('fournisseurs/index.html.twig', [
            'users' => $users,
        ]);
    }
}
