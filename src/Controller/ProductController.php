<?php

namespace App\Controller;

use App\Entity\Produit; 
use App\Form\ProduitType; 
use App\Repository\ProduitRepository; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\OrderItemRepository;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\Notification;
use App\Controller\NotificationsController;

#[Route('/product')]
final class ProductController extends AbstractController
{
    
    #[Route('/revenue', name: 'app_product_revenue', methods: ['GET'])]
    public function getRevenueByProduct(Request $request, OrderRepository $orderRepository): Response
    {
        $user = $this->getUser(); 

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos produits.');
        }

        $revenueData = $orderRepository->getRevenueByProductForUser($user->getId());

        return $this->render('product/revenue.html.twig', [
            'revenueData' => $revenueData,
        ]);
    }
    #[Route(name: 'app_product_index', methods: ['GET'])]
public function index(Request $request, ProduitRepository $produitRepository, NotifierInterface $notifier): Response
{
    $user = $this->getUser(); 

    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos produits.');
    }

    $searchTerm = $request->query->get('search');
    $produits = $produitRepository->findBy(['user' => $user]); // Retrieve all products for the user
    $allProduits = $produitRepository->findBy(['user' => $user]); // Retrieve all products for notifications

    if ($searchTerm) {
        $produits = $produitRepository->searchByTerm($searchTerm, $user);
    }

    // Pagination
    $page = $request->query->getInt('page', 1);
    $limit = 1; // Number of products per page
    $total = count($produits);
    $produits = array_slice($produits, ($page - 1) * $limit, $limit);


        $notifications = []; // Initialize notifications array

        foreach ($allProduits as $produit) {
            $currentTimestamp = time(); // Store the current timestamp
        
            if ($produit->getQuantite() === 0) { // Check stock level for notifications
                // Store the notification data
                $sentAt = $currentTimestamp; // Store the timestamp of when the notification was sent
                $timeAgo = $this->getTimeAgo($sentAt, $currentTimestamp); // Calculate time ago
        
                // Add the notification
                $notifications[] = [
                    'message' => "Le produit '{$produit->getNomprod()}' est en rupture de stock.",
                    'sentAt' => $sentAt,
                    'timeAgo' => $timeAgo, 
                ];
        
                // Send the notification
                $notifier->send(new Notification("Le produit '{$produit->getNomprod()}' est en rupture de stock.", ['chat/slack']));
            } elseif ($produit->getQuantite() < 5) { // Check stock level for notifications
                // Store the notification data
                $sentAt = $currentTimestamp; // Store the timestamp of when the notification was sent
                $timeAgo = $this->getTimeAgo($sentAt, $currentTimestamp); // Calculate time ago
        
                // Add the notification
                $notifications[] = [
                    'message' => "Le produit '{$produit->getNomprod()}' a une quantité faible.",
                    'sentAt' => $sentAt,
                    'timeAgo' => $timeAgo, 
                ];
        
                // Send the notification
                $notifier->send(new Notification("Le produit '{$produit->getNomprod()}' a une quantité faible.", ['chat/slack']));
            }
        }
        
        

        return $this->render('product/index.html.twig', [
    'current_page' => $page,
    'total_pages' => ceil($total / $limit),
            'produits' => $produits,
            'notifications' => $notifications,
        ]);
    }

   
    
    public function getTimeAgo($sentAt, $currentTimestamp) {
        $timeDifference = $currentTimestamp - $sentAt;
    
        if ($timeDifference < 60) {
            return "Just now"; 
        } elseif ($timeDifference < 3600) {
            return floor($timeDifference / 60) . " minute(s) ago";
        } elseif ($timeDifference < 86400) {
            return floor($timeDifference / 3600) . " hour(s) ago";
        } elseif ($timeDifference < 2592000) {
            return floor($timeDifference / 86400) . " day(s) ago";
        } else {
            return floor($timeDifference / 2592000) . " month(s) ago";
        }
    }
    

    #[Route('/new', name: 'app_product_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, NotifierInterface $notifier): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter un produit.');
        }

        $produit = new Produit();
        $produit->setUser($user); 

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
    // Validate image file type
    $imageFile = $form->get('image')->getData();
    if ($imageFile && !in_array($imageFile->guessExtension(), ['jpg', 'jpeg', 'png', 'gif'])) {
        $this->addFlash('error', 'Invalid image format. Please upload a JPG, JPEG, PNG, or GIF file.');
        return $this->redirectToRoute('app_product_new');
    }
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
                    $imageFile->move($uploadDir, $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléversement de l\'image.');
                }

                $produit->setImage('uploads/' . $newFilename);
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            if ($produit->getQuantite() === 0) {
                $notifier->send(new Notification("Le produit '{$produit->getNomprod()}' est en rupture de stock.", ['chat/slack']));
            } elseif ($produit->getQuantite() < 5) {
                $notifier->send(new Notification("Le produit '{$produit->getNomprod()}' a une quantité faible.", ['chat/slack']));
            }

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('product/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager, SluggerInterface $slugger, NotifierInterface $notifier): Response
    {
        $notifications = [
            (object) ['message' => "Le produit 'Peche' est en rupture de stock.", 'timeAgo' => '2 hours ago'],
            (object) ['message' => "Le produit 'Pomme de terre' a une quantité faible.", 'timeAgo' => '1 hour ago']
        ];
        
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    // Déplacer l’image vers 'public/uploads/'
                    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
                    $imageFile->move($uploadDir, $newFilename);

                    if ($produit->getImage() && file_exists($uploadDir . '/' . $produit->getImage())) {
                        unlink($uploadDir . '/' . $produit->getImage());
                    }

                    $produit->setImage('uploads/' . $newFilename);
                } catch (FileException $e) {
                    // Gérer l'erreur d'upload
                    $this->addFlash('error', 'Erreur lors du téléchargement de l’image.');
                }
            }

            // Check product quantity for notifications
            if ($produit->getQuantite() === 0) {
                $notifications[] = "Le produit '{$produit->getNomprod()}' est en rupture de stock.";
                $notifier->send(new Notification("Le produit '{$produit->getNomprod()}' est en rupture de stock.", ['chat/slack']));
            } elseif ($produit->getQuantite() < 5) {
                $notifications[] = "Le produit '{$produit->getNomprod()}' a une quantité faible.";
                $notifier->send(new Notification("Le produit '{$produit->getNomprod()}' a une quantité faible.", ['chat/slack']));
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
            'notifications' => $notifications, // Pass notifications to the view
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager, OrderItemRepository $orderItemRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->getConnection()->beginTransaction();
            
            try {
                $orderItems = $orderItemRepository->findBy(['produit' => $produit]);
                foreach ($orderItems as $orderItem) {
                    $entityManager->remove($orderItem);
                }
                
                $entityManager->remove($produit);
                $entityManager->flush();
                
                $entityManager->getConnection()->commit();
                
                $this->addFlash('success', 'Product and associated order items deleted successfully.');
            } catch (\Exception $e) {
                $entityManager->getConnection()->rollBack();
                $this->addFlash('error', 'An error occurred while deleting the product: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
