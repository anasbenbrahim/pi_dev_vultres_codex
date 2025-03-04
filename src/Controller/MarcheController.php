<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\CategoryRepository;
use App\Entity\User;
use App\Form\MarcheType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Endroid\QrCode\QrCode;
use App\Repository\MarcheRepository;
use Endroid\QrCode\Writer\PngWriter;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Security;
use App\Entity\Marche;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Product;

#[Route('/marche')]
class MarcheController extends AbstractController
{

    #[Route('/', name: 'app_marche', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response
    {
        $marches = $entityManager
            ->getRepository(Produit::class)
            ->findBy(['status' => true]);

        $categories = $categoryRepository->findAll();

        return $this->render('marche/index.html.twig', parameters: [
            'marches' => $marches,
            'categories' => $categories,
        ]);
    }
    //QR CODE 
    #[Route('/{id}/update-status', name: 'app_marche_update_status', methods: ['POST'])]
    public function updateStatus(Produit $product, EntityManagerInterface $entityManager): Response
    {
        // Toggle the status to unavailable
        $product->setStatus(false);
        
        $entityManager->flush();
    
        // Redirect or return a response as needed
        return $this->redirectToRoute('app_marche_index');
    }
    
    #[Route('/generate-qr/{id}', name: 'app_marche_generate_qr', methods: ['GET'])]
    public function generateQrCodeForMarche($id, ProduitRepository $marcheRepository): Response
    {
        $marche = $marcheRepository->find($id);

        // Générer le contenu du QR Code (utilisez toutes les informations du marche)
        $qrContent = sprintf(
            "Nom du produit: %s\nPrix: %s\nQuantité: %s",
            $marche->getNomprod(),
            $marche->getPrix(),
            $marche->getQuantite()
        );

        // Créer une instance de QrCode
        $qrCode = new QrCode($qrContent);

        // Créer une instance de PngWriter pour générer le résultat sous forme d'image PNG
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Créer une réponse avec le résultat du QR Code
        $response = new Response($result->getString(), Response::HTTP_OK, [
            'Content-Type' => $result->getMimeType(),
        ]);

        return $response;
    }
    //QR CODE 


    //BARRE CODE
    #[Route('/generate-barcode/{id}', name: 'app_marche_generate_barcode', methods: ['GET'])]
    public function generateBarcodeForMarche($id, ProduitRepository $marcheRepository): Response
    {
        $marche = $marcheRepository->find($id);

        // Générer le contenu du code-barres (utilisez toutes les informations du marche)
        $barcodeContent = sprintf(
            "Nom du produit: %s\nPrix: %s\nQuantité: %s",
            $marche->getNomprod(),
            $marche->getPrix(),
            $marche->getQuantite()
        );

        // Créer une instance de BarcodeGeneratorHTML
        $generator = new BarcodeGeneratorHTML();

        // Générer le code-barres HTML
        $barcodeHtml = $generator->getBarcode($barcodeContent, $generator::TYPE_CODE_128);

        // Créer une réponse avec le code-barres HTML
        $response = new Response($barcodeHtml, Response::HTTP_OK, [
            'Content-Type' => 'text/html',
        ]);

        return $response;
    }
    // #[Route('/generate-barcode/{id}', name: 'app_marche_generate_barcode', methods: ['GET'])]
    // public function generateBarcodeFormarche($id, marcheRepository $marcheRepository): Response
    // {
    //     $marche = $marcheRepository->find($id);

    //     // Générer le contenu du code-barres (utilisez toutes les informations du marche)
    //     $barcodeContent = sprintf(
    //         "Nom du produit: %s\nPrix: %s\nQuantité: %s",
    //         $marche->getNomprod(),
    //         $marche->getPrix(),
    //         $marche->getQuantite()
    //     );

    //     // Créer une instance de BarcodeGeneratorHTML
    //     $generator = new BarcodeGeneratorHTML();

    //     // Générer le code-barres HTML
    //     $barcodeHtml = $generator->getBarcode($barcodeContent, $generator::TYPE_CODE_128);

    //     // Rendre le modèle Twig associé avec les données nécessaires
    //     return $this->render('marche/codebarre.html.twig', [
    //         'barcodeHtml' => $barcodeHtml,
    //     ]);
    // }
    //DANSN UNE TWIG
    //DANS UNE FLASH
    //   #[Route('/generate-barcode/{id}', name: 'app_marche_generate_barcode', methods: ['GET'])]
    // public function generateBarcodeFormarche($id, marcheRepository $marcheRepository, SessionInterface $session): Response
    // {
    //     $marche = $marcheRepository->find($id);

    //     // Générer le contenu du code-barres (utilisez toutes les informations du marche)
    //     $barcodeContent = sprintf(
    //         "Nom du produit: %s\nPrix: %s\nQuantité: %s",
    //         $marche->getNomprod(),
    //         $marche->getPrix(),
    //         $marche->getQuantite()
    //     );

    //     // Créer une instance de QrCode
    //     $qrCode = new QrCode($barcodeContent);

    //     // Chemin d'enregistrement du fichier image du code-barres
    //     $barcodeImagePath = '/path/to/save/barcode/' . $id . '_barcode.png';

    //     // Enregistrer l'image du code-barres
    //     $qrCode->writeFile($barcodeImagePath);

    //     // Save the barcode image path in the session
    //     $session->set('barcode_image', $barcodeImagePath);

    //     // Flash message
    //     $this->addFlash('barcode', 'Barcode generated successfully!');

    //     // Redirect back to the page
    //     return $this->redirectToRoute('app_marche_index');
    // }


    //BARRE CODE
    #[Route('/back', name: 'app_marcheback_index', methods: ['GET'])]
    public function indexback(EntityManagerInterface $entityManager): Response
    {
        $marches = $entityManager
            ->getRepository(Produit::class)
            ->findAll();

        return $this->render('marche/indexback.html.twig', [
            'marches' => $marches,
        ]);
    }



    #[Route('/new', name: 'app_marche_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Security $security, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $marche = new Marche();
        // $user = new User();
        // $user = $entityManager->getRepository(User::class)->find(7);
        $marche->setIdUser(11);
        $marche->setProdid(12);

        $form = $this->createForm(MarcheType::class, $marche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where your images are stored
                try {
                    $imageFile->move(
                        $this->getParameter('img_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle the exception if something happens during the file upload
                }

                // Update the 'image' property to store the file name instead of its contents
                $marche->setImage($newFilename);
            }

            $entityManager->persist($marche);
            $entityManager->flush();

            return $this->redirectToRoute('app_marche_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('marche/new.html.twig', [
            'marche' => $marche,
            'form' => $form,
        ]);
    }

    #[Route('/download-barcode/{id}', name: 'download_barcode')]
    public function downloadBarcode($id, ManagerRegistry $doctrine): Response
    {
        // Retrieve the marche based on the ID (adjust as needed)
        $marche = $doctrine->getRepository(Produit::class)->find($id);

        if (!$marche) {
            throw $this->createNotFoundException('Marche not found');
        }

        $barcodeContent = sprintf(
            "Nom du produit: %s\nPrix: %s\nQuantité: %s",
            $marche->getNomprod(),
            $marche->getPrix(),
            $marche->getQuantite()
        );

        $generator = new BarcodeGeneratorHTML();

        // Générer le code-barres HTML
        $barcodeHtml = $generator->getBarcode($barcodeContent, $generator::TYPE_CODE_128);

        // Create a response with the barcode content
        $response = new Response($barcodeHtml);

        // Set headers for downloading the file
        $response->headers->set('Content-Type', 'text/html');
        $response->headers->set('Content-Disposition', 'attachment; filename="barcode.html"');

        return $response;
    }


    #[Route('/{id}/edit', name: 'app_marche_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Marche $marche, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MarcheType::class, $marche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_marche_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('marche/edit.html.twig', [
            'marche' => $marche,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_marche_delete', methods: ['POST'])]
    public function delete(Request $request, Marche $marche, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $marche->getId(), $request->request->get('_token'))) {
            $entityManager->remove($marche);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_marche_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}', name: 'app_marche_show', methods: ['GET'])]
    public function show(Marche $marche): Response
    {
        return $this->render('marche/show.html.twig', [
            'marche' => $marche,
        ]);
    }

    #[Route('/marche/{marcheId}/product/{productId}', name: 'app_marchep_show', methods: ['GET'])]
    public function showMarcheProductDetails(int $marcheId, int $productId, ManagerRegistry $doctrine): Response
    {
        // Récupérer le marche
        $marche = $doctrine->getRepository(Marche::class)->find($marcheId);
        if (!$marche) {
            throw $this->createNotFoundException('marche non trouvé.');
        }

        // Récupérer le produit
        $product = $doctrine->getRepository(Produit::class)->find($productId);
        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé.');
        }

        // Passer le marche et le produit à la vue
        return $this->render('marche/product_details.html.twig', [
            'marche' => $marche,
            'product' => $product,
        ]);
    }
    //partie back

    #[Route('/new/back', name: 'app_marcheback_new', methods: ['GET', 'POST'])]
    public function newback(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $marche = new Marche();
        $form = $this->createForm(MarcheType::class, $marche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where your images are stored
                try {
                    $imageFile->move(
                        $this->getParameter('img_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle the exception if something happens during the file upload
                }

                // Update the 'image' property to store the file name instead of its contents
                $marche->setImage($newFilename);
            }

            $entityManager->persist($marche);
            $entityManager->flush();

            return $this->redirectToRoute('app_marcheback_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('marche/newback.html.twig', [
            'marche' => $marche,
            'form' => $form,
        ]);
    }


    #[Route('/back/{id}', name: 'app_marcheback_show', methods: ['GET'])]
    public function showback(Marche $marche): Response
    {
        return $this->render('marche/showback.html.twig', [
            'marche' => $marche,
        ]);
    }

    #[Route('/{id}/edit/back', name: 'app_marcheback_edit', methods: ['GET', 'POST'])]
    public function editback(Request $request, Marche $marche, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MarcheType::class, $marche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_marcheback_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('marche/editback.html.twig', [
            'marche' => $marche,
            'form' => $form,
        ]);
    }

    #[Route('/back/{id}', name: 'app_marcheback_delete', methods: ['POST'])]
    public function deleteback(Request $request, Marche $marche, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $marche->getId(), $request->request->get('_token'))) {
            $entityManager->remove($marche);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_marcheback_index', [], Response::HTTP_SEE_OTHER);
    }
}
