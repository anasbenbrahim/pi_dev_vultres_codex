<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Employee;
use App\Entity\Fermier;
use App\Entity\Fournisseur;
use App\Entity\Superadmin;
use App\Entity\User;
use App\Form\ClientType;
use App\Form\RegistrationFormType;
use App\Security\SecurityAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\FermierType;
use App\Form\FournisseurType;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
public function register(
    Request $request,
    UserPasswordHasherInterface $userPasswordHasher,
    Security $security,
    EntityManagerInterface $entityManager
): Response {
    // Formulaire Fermier
    $fermier = new Fermier();
    $fermierForm = $this->createForm(FermierType::class, $fermier);
    $fermierForm->handleRequest($request);

    if ($fermierForm->isSubmitted() && $fermierForm->isValid()) {
        /** @var string $plainPassword */
        $plainPassword = $fermierForm->get('plainPassword')->getData();

        $fermier->setRoles(['ROLE_FERMIER']);
        $fermier->setPassword($userPasswordHasher->hashPassword($fermier, $plainPassword));

        $entityManager->persist($fermier);
        $entityManager->flush();

        return $security->login($fermier, SecurityAuthenticator::class, 'main');
    }

    // Formulaire Fournisseur
    $fournisseur = new Fournisseur();
    $fournisseurForm = $this->createForm(FournisseurType::class, $fournisseur);
    $fournisseurForm->handleRequest($request);

    if ($fournisseurForm->isSubmitted() && $fournisseurForm->isValid()) {
        /** @var string $plainPassword */
        $plainPassword = $fournisseurForm->get('plainPassword')->getData();

        $fournisseur->setRoles(['ROLE_FOURNISSEUR']);
        $fournisseur->setPassword($userPasswordHasher->hashPassword($fournisseur, $plainPassword));

        $entityManager->persist($fournisseur);
        $entityManager->flush();

        return $security->login($fournisseur, SecurityAuthenticator::class, 'main');
    }

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

    // Passer les deux formulaires à la vue Twig
    return $this->render('registration/register.html.twig', [
        'fermierType' => $fermierForm->createView(),
        'fournissuerType' => $fournisseurForm->createView(),
        'clientFormType' => $clientForm->createView(),
    ]);
}

    

    
}
