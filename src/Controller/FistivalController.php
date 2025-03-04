<?php

namespace App\Controller;

use App\Entity\Fistival;
use App\Form\FistivalType;
use App\Repository\FistivalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/fistival')]
final class FistivalController extends AbstractController
{
    #[Route(name: 'app_fistival_index', methods: ['GET'])]
    public function index(FistivalRepository $fistivalRepository): Response
    {
        return $this->render('fistival/index.html.twig', [
            'fistivals' => $fistivalRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_fistival_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $fistival = new Fistival();
        $form = $this->createForm(FistivalType::class, $fistival);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
            
                try {
                    // Define the upload directory (relative to the 'public' folder)
                    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads'; // Path to the public/uploads directory
            
                    // Move the uploaded image to the upload directory
                    $imageFile->move(
                        $uploadDir,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle error during file upload
                    $this->addFlash('error', 'There was an error uploading the image.');
                }
            
                // Store the relative path to the image
                $fistival->setphoto('uploads/' . $newFilename); // Save the relative path in the database
            }
            $entityManager->persist($fistival);
            $entityManager->flush();

            // Redirect to the product list after saving
            return $this->redirectToRoute('app_fistival_index');
        }

        // Render the form for adding a new product
        return $this->render('fistival/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_fistival_show', methods: ['GET'])]
    public function show(Fistival $fistival): Response
    {
        return $this->render('fistival/show.html.twig', [
            'fistival' => $fistival,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fistival_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fistival $fistival, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FistivalType::class, $fistival);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_fistival_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fistival/edit.html.twig', [
            'fistival' => $fistival,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fistival_delete', methods: ['POST'])]
    public function delete(Request $request, Fistival $fistival, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fistival->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($fistival);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_fistival_index', [], Response::HTTP_SEE_OTHER);
    }
}
