<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Form\FournisseurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Bundle\SecurityBundle\Security;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/fournisseur')]
final class FournisseurController extends AbstractController
{
    #[Route(name: 'app_fournisseur_index')]
    public function index(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security): Response
    {
        $fournisseurs = $entityManager
            ->getRepository(Fournisseur::class)
            ->findAll();

            $fournisseur = new Fournisseur();
            $fournisseurForm = $this->createForm(FournisseurType::class, $fournisseur);
            $fournisseurForm->handleRequest($request);

            if ($fournisseurForm->isSubmitted() && $fournisseurForm->isValid()) {
                /** @var string $plainPassword */
                $plainPassword = $fournisseurForm->get('plainPassword')->getData();
        
                $fournisseur->setRoles(['ROLE_Fournisseur']);
                $fournisseur->setPassword($userPasswordHasher->hashPassword($fournisseur, $plainPassword));
        
                $entityManager->persist($fournisseur);
                $entityManager->flush();
        
                return $this->redirectToRoute('app_fournisseur_index');
            }

        return $this->render('fournisseur/index.html.twig', [
            'fournisseurs' => $fournisseurs,
            'fournisseurFormType' => $fournisseurForm->createView(),
        ]);
    }

    #[Route('/new', name: 'app_fournisseur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fournisseur = new Fournisseur();
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fournisseur);
            $entityManager->flush();

            return $this->redirectToRoute('app_fournisseur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fournisseur/new.html.twig', [
            'fournisseur' => $fournisseur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fournisseur_show', methods: ['GET'])]
    public function show(Fournisseur $fournisseur): Response
    {
        return $this->render('fournisseur/show.html.twig', [
            'fournisseur' => $fournisseur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fournisseur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fournisseur $fournisseur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_fournisseur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fournisseur/edit.html.twig', [
            'fournisseur' => $fournisseur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fournisseur_delete', methods: ['POST'])]
    public function delete(Request $request, Fournisseur $fournisseur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fournisseur->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($fournisseur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_fournisseur_index', [], Response::HTTP_SEE_OTHER);
    }
}
