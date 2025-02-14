<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Security\SecurityAuthenticator;


use Symfony\Bundle\SecurityBundle\Security;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/customer')]
final class CustomerController extends AbstractController
{
    #[Route(name: 'app_customer_index')]
    public function index(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security): Response
    {
        $clients = $entityManager
            ->getRepository(Client::class)
            ->findAll();

            $client = new Client();
            $clientForm = $this->createForm(ClientType::class, $client);
            $clientForm->handleRequest($request);

            if ($clientForm->isSubmitted() && $clientForm->isValid()) {
                /** @var string $plainPassword */
                $plainPassword = $clientForm->get('plainPassword')->getData();
        
                $client->setRoles(['ROLE_CLIENT']);
                $client->setPassword($userPasswordHasher->hashPassword($client, $plainPassword));
        
                $entityManager->persist($client);
                $entityManager->flush();
        
                return $this->redirectToRoute('app_customer_index');
            }

        return $this->render('customer/index.html.twig', [
            'clients' => $clients,
            'clientFormType' => $clientForm->createView(),
        ]);
    }

    #[Route('/new', name: 'app_new_customer')]
    public function registerClient(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager ): Response
    {
        $client = new Client();
        $clientForm = $this->createForm(ClientType::class, $client);
        $clientForm->handleRequest($request);

        if ($clientForm->isSubmitted() && $clientForm->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $clientForm->get('plainPassword')->getData();
    
            $client->setRoles(['ROLE_CLIENT']);
            $client->setPassword($userPasswordHasher->hashPassword($client, $plainPassword));
    
            $entityManager->persist($client);
            $entityManager->flush();
    
            return $security->login($client, SecurityAuthenticator::class, 'main');
        }

        return $this->render('customer/index.html.twig', [
            'clientFormType' => $clientForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_customer_show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('customer/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_customer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('customer/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_customer_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
    }
}
