<?php

namespace App\Controller;

use App\Entity\Fermier;
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
    #[Route(name: 'app_fermier_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $fermiers = $entityManager
            ->getRepository(Fermier::class)
            ->findAll();

        return $this->render('fermier/index.html.twig', [
            'fermiers' => $fermiers,
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $fermier = new Fermier();
        $form = $this->createForm(FermierType::class, $fermier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $fermier->setRoles('ROLE_FERMIER');
            // encode the plain password
            $fermier->setPassword($userPasswordHasher->hashPassword($fermier, $plainPassword));

            $entityManager->persist($fermier);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($fermier, SecurityAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'fermierType' => $form,
            'firstName' => $form->get('firstName')->createView(),
            'lastName' => $form->get('lastName')->createView(),
            'email' => $form->get('email')->createView(),
            'plainPassword' => $form->get('plainPassword')->createView(),
            'farmName' => $form->get('farmName')->createView(),
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
