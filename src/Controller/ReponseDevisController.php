<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Devis;
use App\Entity\ReponseDevis;
use App\Entity\User;
use App\Repository\DevisRepository;
use App\Repository\EquipementsRepository;
use App\Form\ReponseDevisType;
use App\Repository\ReponseDevisRepository;

final class ReponseDevisController extends AbstractController
{
    #[Route('/reponse/devis/{id}', name: 'app_reponse_devis')]
    public function Repense(Request $request,ManagerRegistry $doctrine,$id,DevisRepository $repo_devis,EquipementsRepository $repo_equipement,ReponseDevisType $form)
    {
        $reponse=new ReponseDevis();
        
        $devis=$repo_devis->find($id);
        $equipement_demandee=$devis->getEquipement();
        $id_equipement=$equipement_demandee->getId();
        $equipement=$repo_equipement->find($id_equipement);
        $fermier=$devis->getFermier();
        $reponse->setDevis($devis);
        $reponse->setFournisseur($devis->getFournisseur());
        if($devis->getQuantite() > $equipement->getQuantite()){
            $reponse->setReponse("Nous n'avons pas la quantite demande dans le stock");
            return $this->redirectToRoute('show_devis'); 

        }
        else{
            $reponse->setFermier($fermier);
            $form=$this->createForm(ReponseDevisType::class,$reponse);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $reponse=$form->getData();
                $em=$doctrine->getManager();
                $em->persist($reponse);
                $em->flush();
                return $this->redirectToRoute('show_devis'); 
            }
        }
        return $this->render('reponse_devis/reponse.html.twig',['form'=>$form,'equipement'=>$equipement_demandee]);
    }
    #[Route('/show_reponse_devis','show_reponse')]
    public function show_reponse(DevisRepository $repodevis,ReponseDevisRepository $repoReponse){
        $user=$this->getUser();
        if($user instanceof User )
        {
            $id=$user->getId();
            $devis=$repodevis->findBy(['fermier'=>$id]);
            $reponse=$repoReponse->findBy(['fermier'=>$id]);
            return $this->render('reponse_devis/show_reponse.html.twig',["liste_reponse"=>$reponse,"liste_devis"=>$devis]);   
        }
        else
            return $this->redirectToRoute('app_login'); // Redirect if not authenticated


        /*$id_devis=$devis->getId();
        $reponse=$repodevis->find(['devis'=>$id_devis]);
        if(!$reponse){
            return $this->redirectToRoute('');
        }
        else
            return $this->render('reponse_devis/show_reponse.html.twig',["reponse"=>$reponse]);*/
    }
}
