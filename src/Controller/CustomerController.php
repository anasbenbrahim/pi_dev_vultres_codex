<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientForm;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Security\SecurityAuthenticator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




use App\Service\PasswordGenerator;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;



#[Route('/customer')]
final class CustomerController extends AbstractController
{
    

   
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
