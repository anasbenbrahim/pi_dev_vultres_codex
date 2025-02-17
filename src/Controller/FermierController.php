<?php

namespace App\Controller;

use App\Entity\Fermier;
use App\Form\FermierForm;
use App\Form\FermierType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Security\SecurityAuthenticator;








#[Route('/fermier')]
final class FermierController extends AbstractController
{

    #[Route(name: 'app_fermier')]
    public function index(): Response
    {
        
        return $this->render('fermier/home.html.twig', [
            'controller_name' => 'FermierController',
        ]);
    }
    
    #[Route('/new', name: 'app_new_fermier')]
    public function registerClient(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager ): Response
    {
        $client = new Fermier();
        $clientForm = $this->createForm(FermierType::class, $client);
        $clientForm->handleRequest($request);

        if ($clientForm->isSubmitted() && $clientForm->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $clientForm->get('plainPassword')->getData();
    
            $client->setRoles(['ROLE_FERMIER']);
            $client->setPassword($userPasswordHasher->hashPassword($client, $plainPassword));
    
            $entityManager->persist($client);
            $entityManager->flush();
    
            return $security->login($client, SecurityAuthenticator::class, 'main');
        }

        return $this->render('fermier/index.html.twig', [
            'clientFormType' => $clientForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_fermier_show', methods: ['GET'])]
    public function show(Fermier $fermier): Response
    {
        return $this->render('fermier/show.html.twig', [
            'fermier' => $fermier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fermier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fermier $fermier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FermierType::class, $fermier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_fermier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fermier/edit.html.twig', [
            'fermier' => $fermier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fermier_delete', methods: ['POST'])]
    public function delete(Request $request, Fermier $fermier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fermier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($fermier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_fermier_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
