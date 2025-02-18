<?php

namespace App\Controller;


use App\Entity\Produit; // Changed Produit to Product
use App\Form\ProduitType; // Changed ProduitType to ProductType
use App\Repository\ProduitRepository; // Changed ProduitRepository to ProduitRepository
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\OrderItemRepository;

#[Route('/product')]
final class ProductController extends AbstractController
{
    #[Route(name: 'app_product_index', methods: ['GET'])]
public function index(ProduitRepository $produitRepository): Response
{
    $user = $this->getUser(); // Récupère l'utilisateur connecté

    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos produits.');
    }

    // Récupérer uniquement les produits de l'utilisateur connecté
    $produits = $produitRepository->findBy(['user' => $user]);

    return $this->render('product/index.html.twig', [
        'produits' => $produits,
    ]);
}

    
#[Route('/new', name: 'app_product_new')]
public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    // Récupérer l'utilisateur connecté
    $user = $this->getUser();
    if (!$user) {
        throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter un produit.');
    }

    $produit = new Produit();
    $produit->setUser($user); 

    $form = $this->createForm(ProduitType::class, $produit);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                // Définir le répertoire d'upload (dans le dossier 'public/uploads')
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';

                // Déplacer l'image téléchargée vers le répertoire défini
                $imageFile->move(
                    $uploadDir,
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('error', 'Une erreur est survenue lors du téléversement de l\'image.');
            }

            // Enregistrer le chemin relatif de l'image dans la base de données
            $produit->setImage('uploads/' . $newFilename);
        }

        $entityManager->persist($produit);
        $entityManager->flush();

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
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
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
    
                    // Supprimer l'ancienne image si nécessaire
                    if ($produit->getImage() && file_exists($uploadDir . '/' . $produit->getImage())) {
                        unlink($uploadDir . '/' . $produit->getImage());
                    }
    
                    // Mettre à jour le champ image avec le nouveau nom de fichier
                    $produit->setImage('uploads/' . $newFilename);
                } catch (FileException $e) {
                    // Gérer l'erreur d'upload
                    $this->addFlash('error', 'Erreur lors du téléchargement de l’image.');
                }
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('product/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager, OrderItemRepository $orderItemRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->getPayload()->getString('_token'))) {
            // Begin transaction
            $entityManager->getConnection()->beginTransaction();
            
            try {
                // Delete associated order items
                $orderItems = $orderItemRepository->findBy(['produit' => $produit]);
                foreach ($orderItems as $orderItem) {
                    $entityManager->remove($orderItem);
                }
                
                // Remove the product
                $entityManager->remove($produit);
                $entityManager->flush();
                
                // Commit transaction
                $entityManager->getConnection()->commit();
                
                $this->addFlash('success', 'Product and associated order items deleted successfully.');
            } catch (\Exception $e) {
                // Rollback transaction on error
                $entityManager->getConnection()->rollBack();
                $this->addFlash('error', 'An error occurred while deleting the product: ' . $e->getMessage());
            }
        }


        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }


}
