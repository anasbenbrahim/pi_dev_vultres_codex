<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\DevisRepository;
use App\Entity\Devis;
use App\Entity\ReponseDevis;
use App\Entity\User;
use App\Repository\ReponseDevisRepository;
use App\Repository\EquipementsRepository;


use Stripe;

final class PaymentController extends AbstractController
{
    #[Route('/payment/{id}', name: 'app_stripe')]
    public function index($id,ReponseDevisRepository $repo): Response
    {
        $reponse_devis=$repo->find($id);
        return $this->render('payment/index.html.twig', [
            'stripe_key' => $_ENV["STRIPE_KEY"],
            "devis"=>$reponse_devis
        ]);
    }
    #[Route(path: '/stripe/create-charge/{id}', name: 'app_stripe_charge')]
    public function checkout(Request $request,$id,ReponseDevisRepository $repo,DevisRepository $repo_devis,ManagerRegistry $doctrine): Response
    {
        $em=$doctrine->getManager();
        $reponse=$repo->find($id);
        $devis=$reponse->getDevis();
        $proposition=$devis->getProposition();
        //dd($devis);
        //$devis=$repo_devis->find($devis_id);
        //$devis=$repo_devis->find($id);
        if (!$devis) {
            $this->addFlash('error', 'Devis not found!');
            return $this->redirectToRoute('app_stripe', ['id' => $id], Response::HTTP_SEE_OTHER);
        }
        $equipement=$devis->getEquipement();

        if (!$reponse) {
            $this->addFlash('error', 'ReponseDevis not found!');
            return $this->redirectToRoute('app_stripe', ['id' => $id], Response::HTTP_SEE_OTHER);
        }
        //dump($reponse);
        if($reponse->getEtat() == true){

            $equipement->setQuantite($equipement->getQuantite() - $devis->getQuantite());
            $em->persist($equipement);
            $em->flush();
            $prix=$devis->getQuantite() * $reponse->getPrix();
            //$prix=540;
        }
        Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
        Stripe\Charge::create ([
                "amount" => $prix * 100,
                "currency" => "usd",
                "source" => $request->request->get('stripeToken'),
                "description" => $proposition
        ]);
        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        return $this->redirectToRoute('app_stripe',  ['id' => $id], Response::HTTP_SEE_OTHER);
    }

}
