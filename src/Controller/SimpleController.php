<?php

namespace App\Controller;

use App\Entity\Simple;
use App\Form\SimpleType;
use App\Repository\SimpleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/simple')]
final class SimpleController extends AbstractController
{
    #[Route(name: 'app_simple_index', methods: ['GET'])]
    public function index(SimpleRepository $simpleRepository): Response
    {
        return $this->render('simple/index.html.twig', [
            'simples' => $simpleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_simple_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $simple = new Simple();
        $form = $this->createForm(SimpleType::class, $simple);
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
                $simple->setphoto('uploads/' . $newFilename); // Save the relative path in the database
            }
            $entityManager->persist($simple);
            $entityManager->flush();

            // Redirect to the product list after saving
            return $this->redirectToRoute('app_simple_index');
        }

        // Render the form for adding a new product
        return $this->render('simple/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_simple_show', methods: ['GET'])]
    public function show(Simple $simple): Response
    {
        return $this->render('simple/show.html.twig', [
            'simple' => $simple,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_simple_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Simple $simple, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SimpleType::class, $simple);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_simple_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('simple/edit.html.twig', [
            'simple' => $simple,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_simple_delete', methods: ['POST'])]
    public function delete(Request $request, Simple $simple, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$simple->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($simple);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_simple_index', [], Response::HTTP_SEE_OTHER);
    }
}
