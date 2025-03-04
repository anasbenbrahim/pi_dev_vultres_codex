<?php
// src/Controller/OllamaController.php
namespace App\Controller;

use App\Service\OllamaClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ChatAiRepository;
use App\Entity\ChatAi;
use App\Form\AiChatType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class OllamaController extends AbstractController
{
    private OllamaClient $ollamaClient;

    public function __construct(OllamaClient $ollamaClient)
    {
        $this->ollamaClient = $ollamaClient;
    }


    #[Route('/generate-ollama', name: 'generate_ollama')]
    public function generate(Request $req,OllamaClient $ollamaClient,ChatAiRepository $repo,ManagerRegistry $mr): Response
    {
        $quest=new ChatAi();
        $form=$this->createForm(AiChatType::class,$quest);
        $form->handleRequest($req);
        $rm=$mr->getManager();
        if($form->isSubmitted()){
            $quest=$form->getData();
            $rm->persist($quest);
            $rm->flush();
            $data = $ollamaClient->fetchLlamaResponse($repo);
            return $this->render('/openai/index.html.twig', [
                'responseData' => $data,
                'form' => $form->createView()
            ]);
        }

        return $this->render('/openai/index.html.twig', [
            'responseData' => null,
            'form' => $form->createView()
        ]);
        //return $this->json($data);
    }

}
/*

*/