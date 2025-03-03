<?php

namespace App\Controller;


use App\Repository\PublicationRepository;
use App\Entity\Client;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Commentaire;
use App\Entity\Event;
use App\Entity\Publication;
use App\Entity\Reclamation;
use App\Entity\Notification;
use App\Service\NotificationService;
use App\Form\ClientForm;
use App\Form\ClientType;
use App\Form\CommentaireType;
use App\Form\PasswordForm;
use App\Form\PublicationType;
use App\Form\ReclamationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Cloudinary\Cloudinary;
use App\Repository\OfferRepository;
use App\Entity\Offer;

#[Route('/')]
final class ClientController extends AbstractController
{
    #[Route( name: 'app_client')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $publications = $entityManager->getRepository(Publication::class)->findAll();
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
            'publications' => $publications,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function editClient(Request $request, Client $patient, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(ClientType::class, $patient, [
            'is_edit' => true, // L'utilisateur connecté, donc on n'affiche pas le champ de mot de passe
        ]);
        $form->handleRequest($request);

        $formPassword = $this->createForm(PasswordForm::class);
        $formPassword->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_client', [], Response::HTTP_SEE_OTHER);
        }

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $newPassword = $formPassword->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($patient, $newPassword);
            $patient->setPassword($hashedPassword);
            $entityManager->flush();
            return $this->redirectToRoute('app_client', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('customer/client_edit.html.twig', [
            'patient' => $patient,
            'form' => $form->createView(),
            'formP' => $formPassword->createView(),
        ]);
    }



    #[Route('/publicationclient', name: 'publication_client')]
    public function indexPublication(EntityManagerInterface $entityManager): Response
    {
        
        $publications = $entityManager->getRepository(Publication::class)->findAll();

        return $this->render('publication/index.html.twig', [
            'publications' => $publications,
        ]);
    }

    private $cloudinary;

    public function __construct(Cloudinary $cloudinary)
    {
        $this->cloudinary = $cloudinary;
    }

    #[Route('/publication/new', name: 'publication_new')]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $publication = new Publication();
    $publication->setDate(new \DateTime());
    $client = $this->getUser();
    $publication->setClient($client);

    $form = $this->createForm(PublicationType::class, $publication);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle image upload if the user chooses to upload a file
        if ($publication->getImageChoice() === 'upload') {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $uploadResult = $this->cloudinary->uploadApi()->upload($imageFile->getPathname(), [
                    'folder' => 'publications_images',
                ]);
                $publication->setImageUrl($uploadResult['secure_url']);
            }
        }

        // Handle URL image if the user chooses to provide a URL
        if ($publication->getImageChoice() === 'url') {
            $url = $form->get('image')->getData();
            if ($url) {
                $publication->setImageUrl($url); // Store the URL directly
            }
        }

        // Persist the publication entity
        $entityManager->persist($publication);
        $entityManager->flush();

        return $this->redirectToRoute('publication_client');
    }

    return $this->render('publication/new.html.twig', [
        'publication' => $publication,
        'form' => $form->createView(),
    ]);
}






    #[Route('/{id}/delete', name: 'publication_delete')]
    public function deletepub(Publication $publication, EntityManagerInterface $entityManager): Response
    {
        foreach ($publication->getCommentaires() as $commentaire) {
            $entityManager->remove($commentaire);
        }

        $entityManager->remove($publication);
        $entityManager->flush();

        return $this->redirectToRoute('publication_client');
    }


    #[Route('/publication/{id}/edit', name: 'publication_edit')]
public function editPublication(Request $request, Publication $publication, EntityManagerInterface $entityManager): Response
{
    if (!$publication->getDate()) {
        $publication->setDate(new \DateTime());
    }

    $form = $this->createForm(PublicationType::class, $publication);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle image upload if the user chooses to upload a file
        if ($publication->getImageChoice() === 'upload') {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $uploadResult = $this->cloudinary->uploadApi()->upload($imageFile->getPathname(), [
                    'folder' => 'publications_images',
                ]);
                $publication->setImageUrl($uploadResult['secure_url']);
            }
        }

        // Handle URL image if the user chooses to provide a URL
        if ($publication->getImageChoice() === 'url') {
            $url = $form->get('image')->getData();
            if ($url) {
                $publication->setImageUrl($url); // Store the URL directly
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('publication_client');
    }

    return $this->render('publication/edit.html.twig', [
        'publication' => $publication,
        'form' => $form->createView(),
    ]);
}

    
#[Route('/publication/{id}', name: 'publication_show')]
public function show(Publication $publication, Request $request, EntityManagerInterface $entityManager): Response
{
    $client = $publication->getClient(); // Owner of the publication
    $commentaire = new Commentaire();
    $commentaire->setPublication($publication);
    $commentaire->setClient($this->getUser());

    $commentaireForm = $this->createForm(CommentaireType::class, $commentaire);
    $commentaireForm->handleRequest($request);

    if ($commentaireForm->isSubmitted() && $commentaireForm->isValid()) {
        // Clean the comment
        $badWords = ['test', 'test2', 'test3'];
        $cleanedComment = str_ireplace($badWords, '***', $commentaire->getDescription());
        $commentaire->setDescription($cleanedComment);

        // Persist the comment
        $entityManager->persist($commentaire);
        $entityManager->flush();

        // Always send notification to the publication owner (client)
        if ($client !== $this->getUser()) {
            $notification = new Notification();
            $notification->setMessage("New comment on your publication: " . $publication->getTitre() . " by " . $this->getUser()->getLastName())
                         ->setPublication($publication)
                         ->setReading(false)
                         ->setClient($client); // Send notification to the publication owner
            $entityManager->persist($notification);
            $entityManager->flush();
        }

       
        return $this->redirectToRoute('publication_show', ['id' => $publication->getId()]);
    }

    // Mark notifications as read when the owner clicks on a notification
    if ($request->get('notification_id')) {
        $notification = $entityManager->getRepository(Notification::class)->find($request->get('notification_id'));
        if ($notification && $notification->getClient() === $this->getUser()) {
            $notification->setReading(true);
            $entityManager->flush();
        }
    }

    return $this->render('publication/show.html.twig', [
        'publication' => $publication,
        'client' => $client,
        'commentaires' => $publication->getCommentaires(),
        'commentaire_form' => $commentaireForm->createView(),
    ]);
}






    #[Route('/commentaire/', name: 'commentaire_index')]
    public function indexcommentaire(EntityManagerInterface $entityManager): Response
    {
        $commentaires = $entityManager->getRepository(Commentaire::class)->findAll();
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }

    #[Route('/commentaire/new', name: 'commentaire_new')]
    public function newcommentaire(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commentaire);
            $entityManager->flush();
            return $this->redirectToRoute('commentaire_index');
        }

        return $this->render('commentaire/new.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/commentaire/{id}/edit', name: 'commentaire_edit')]
public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
{
    $commentaire = $entityManager->getRepository(Commentaire::class)->find($id);

    if (!$commentaire) {
        throw new NotFoundHttpException('Commentaire not found');
    }

    $form = $this->createForm(CommentaireType::class, $commentaire);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $badWords = ['test', 'test2', 'test3'];
        $cleanedComment = str_ireplace($badWords, '***', $commentaire->getDescription());
        $commentaire->setDescription($cleanedComment);

        $entityManager->flush();

        return $this->redirectToRoute('publication_show', ['id' => $commentaire->getPublication()->getId()]);
    }

    return $this->render('commentaire/edit.html.twig', [
        'commentaire' => $commentaire,
        'form' => $form->createView(),
    ]);
}


    #[Route('/commentaire/delete/{id}', name: 'commentaire_delete')]
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $commentaire = $entityManager->getRepository(Commentaire::class)->find($id);

        if (!$commentaire) {
            throw new NotFoundHttpException('Commentaire not found');
        }

        $publicationId = $commentaire->getPublication()->getId();

        $entityManager->remove($commentaire);
        $entityManager->flush();

        return $this->redirectToRoute('publication_show', ['id' => $publicationId]);
    }


    #[Route('/reclamation', name: 'reclamation_index')]
public function indexreclamation(EntityManagerInterface $entityManager): Response
{
    $client = $this->getUser();

    if (!$client) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
    }
    $reclamations = $entityManager->getRepository(Reclamation::class)->findBy(['client' => $client]);

    return $this->render('reclamation/index.html.twig', [
        'reclamations' => $reclamations,
    ]);
}


#[Route('/reclamation/new/{publicationId}', name: 'reclamation_new')]
public function newReclamation(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, $publicationId): Response
{
    $publication = $entityManager->getRepository(Publication::class)->find($publicationId);

    if (!$publication) {
        throw $this->createNotFoundException('Publication not found');
    }

    $reclamation = new Reclamation();
    $reclamation->setDate(new \DateTime());
    $reclamation->setPublication($publication);
    $client = $this->getUser();
    $reclamation->setClient($client);

    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($reclamation);
        $entityManager->flush();
        $email = (new Email())
            ->from('amine.graja589@gmail.com')
            ->to('anasbenbrahim491@gmail.com')
            ->subject('New Reclamation Submitted')
            ->html('<p>A new reclamation has been submitted for publication: ' . $reclamation->getPublication()->getTitre() . '</p>');
        $mailer->send($email);
        return $this->redirectToRoute('reclamation_index');
    }
    return $this->render('reclamation/new.html.twig', [
        'reclamation' => $reclamation,
        'form' => $form->createView(),
    ]);
}




#[Route('/publications/search', name: 'publication_search', methods: ['GET'])]
public function search(Request $request, PublicationRepository $publicationRepository): Response
{
    $titre = $request->query->get('titre', '');

    $publications = $publicationRepository->createQueryBuilder('p')
        ->where('p.titre LIKE :titre')
        ->setParameter('titre', '%' . $titre . '%')
        ->getQuery()
        ->getResult();

    if ($request->isXmlHttpRequest()) {
        // Return the list as a partial template when it's an AJAX request
        return $this->render('publication/_list.html.twig', [
            'publications' => $publications
        ]);
    }

    // For regular requests, return the full page with the search term and list
    return $this->render('publication/index.html.twig', [
        'publications' => $publications,
        'searchTerm' => $titre
    ]);
}

    



    #[Route('/reclamation/{id}', name: 'reclamation_show')]
    public function showreclammation(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/reclamation/{id}/edit', name: 'reclamation_edit')]
    public function editreclammation(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($reclamation->getDate() === null) {
            $reclamation->setDate(new \DateTime());
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('reclamation_index');
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reclamation/{id}/delete', name: 'reclamation_delete')]
    public function deletereclammation(Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($reclamation);
        $entityManager->flush();
        return $this->redirectToRoute('reclamation_index');
    }

    #[Route('/event/test',name: 'app_event_indexclient',methods: ['GET'])]
    public function test(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findAll();
        return $this->render('event/showevent.html.twig',['events' => $events]);
    }
    #[Route('/event/detail/{id}','event_detail_client',methods: ['GET'])]
    public function detail(EntityManagerInterface $entityManager,$id): Response
    {
        $events = $entityManager->getRepository(Event::class)->find($id);
        return $this->render('event/detaille.html.twig',['event' => $events]);
    }

    #[Route('/client/{clientId}/reclamations', name: 'client_reclamations')]
public function clientReclamations(int $clientId, EntityManagerInterface $entityManager): Response
{
    $client = $entityManager->getRepository(Client::class)->find($clientId);

    if (!$client) {
        throw $this->createNotFoundException('Client not found');
    }

    $reclamations = $entityManager->getRepository(Reclamation::class)->findBy(['client' => $client]);

    return $this->render('reclamation/index.html.twig', [
        'client' => $client,
        'reclamations' => $reclamations,
    ]);
}




    #[Route('/clientProfile', name: 'app_profile_client')]
    public function indexprofileclient(): Response
    {

        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        return $this->render('customer/show.html.twig', [
            'controller_name' => 'ProfileController',
            'client' => $user,
        ]);
    }

    #[Route('/notifications', name: 'notifications')]
public function notifications(Request $request, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser(); // Get the logged-in user

    // Get the notifications related to publications that the user is involved with
    $notifications = $entityManager->getRepository(Notification::class)
        ->createQueryBuilder('n')
        ->innerJoin('n.publication', 'p')
        ->where('p.client = :user OR EXISTS (SELECT 1 FROM App\Entity\Commentaire c WHERE c.publication = p AND c.client = :user)')
        ->setParameter('user', $user)
        ->getQuery()
        ->getResult();

    return $this->render('notification/list.html.twig', [
        'notifications' => $notifications,
    ]);
}



    #[Route('/notification/{id}/read', name: 'notification_read')]
    public function markAsRead(Notification $notification, EntityManagerInterface $entityManager): Response
    {
        $notification->setReading(true);
        $entityManager->flush();
        return $this->redirectToRoute('notifications');
    }

    #[Route('/publication/{id}/clear_notifications', name: 'publication_clear_notifications')]
    public function clearNotifications(Publication $publication, EntityManagerInterface $entityManager): Response
    {
        $notifications = $entityManager->getRepository(Notification::class)->findBy(['publication' => $publication]);
        foreach ($notifications as $notification) {
            $entityManager->remove($notification);
        }
        $entityManager->flush();
        $this->addFlash('success', 'Toutes les notifications ont été supprimées.');
        return $this->redirectToRoute('publication_show', ['id' => $publication->getId()]);
    }


    

    



}
