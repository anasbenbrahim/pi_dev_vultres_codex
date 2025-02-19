<?php

// src/EventListener/AccessDeniedListener.php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class AccessDeniedListener
{
    private $router;
    private $twig;

    public function __construct(RouterInterface $router, Environment $twig)
    {
        $this->router = $router;
        $this->twig = $twig;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        // Vérifier si l'exception est une AccessDeniedException
        if ($exception instanceof AccessDeniedHttpException) {
            // Rendre le template Twig pour l'erreur 404
            $content = $this->twig->render('bundles/TwigBundle/Exception/error404.html.twig');
            $response = new Response($content, Response::HTTP_NOT_FOUND);
            $event->setResponse($response);
        }
    }
}