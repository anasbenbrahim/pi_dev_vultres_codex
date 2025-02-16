<?php

namespace App\Controller;

use App\Entity\Superadmin;
use App\Form\SuperadminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/superadmin')]
final class SuperadminController extends AbstractController
{
    #[Route(name: 'app_superadmin_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $superadmins = $entityManager
            ->getRepository(Superadmin::class)
            ->findAll();

        return $this->render('superadmin/index.html.twig', [
            'superadmins' => $superadmins,
        ]);
    }

    #[Route('/new', name: 'app_superadmin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $superadmin = new Superadmin();
        $form = $this->createForm(SuperadminType::class, $superadmin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($superadmin);
            $entityManager->flush();

            return $this->redirectToRoute('app_superadmin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('superadmin/new.html.twig', [
            'superadmin' => $superadmin,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_superadmin_show', methods: ['GET'])]
    public function show(Superadmin $superadmin): Response
    {
        return $this->render('superadmin/show.html.twig', [
            'superadmin' => $superadmin,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_superadmin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Superadmin $superadmin, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SuperadminType::class, $superadmin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_superadmin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('superadmin/edit.html.twig', [
            'superadmin' => $superadmin,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_superadmin_delete', methods: ['POST'])]
    public function delete(Request $request, Superadmin $superadmin, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$superadmin->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($superadmin);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_superadmin_index', [], Response::HTTP_SEE_OTHER);
    }
}
