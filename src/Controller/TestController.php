<?php

// src/Controller/TestController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TestController extends AbstractController
{
    #[Route('/test-access-denied', name: 'test_access_denied')]
    public function testAccessDenied(): Response
    {
        throw new AccessDeniedException('Accès refusé !');
    }
}