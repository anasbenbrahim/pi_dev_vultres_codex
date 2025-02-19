<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FermiersController extends AbstractController
{

    

    #[Route('/fermiers', name: 'app_fermiers')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('fermiers/index.html.twig', [
            'users' => $users,
        ]);
    }
}
