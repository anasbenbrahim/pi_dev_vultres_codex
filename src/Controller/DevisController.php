<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\DevisType;
use App\Entity\Equipements;
use App\Entity\Devis;
use App\Repository\DevisRepository;
use App\Repository\EquipementsRepository;

final class DevisController extends AbstractController
{
    #[Route('/demande_devis/{id}',name: 'demande_devis')]
    public function create_devis(ManagerRegistry $doctrine,Request $req,EquipementsRepository $repo,$id): Response{
        
        $devis=new Devis();
        $equipement=new Equipements();
        $equipement=$repo->find($id);
        $fournisseur=$equipement->getUser();
        if (!$equipement) {
            throw $this->createNotFoundException('Equipement not found');
        }
        $client=$this->getUser();
        dump($client);
        //if($client->getRoles()=='ROLE_FERMIER');
        
            $devis->setEquipement($equipement);
            $em=$doctrine->getManager();
            $form=$this->createForm(DevisType::class,$devis);
            $form->handleRequest($req);
            if($form->isSubmitted() && $form->isValid()){
                $devis=$form->getData();
                $devis->setFournisseur($fournisseur);
                $devis->setFermier($client);
                $em->persist($devis);
                $em->flush();
                return $this->redirectToRoute('show_equipement');
            }
        
        return $this->render('/devis/index.html.twig',['form' =>$form,"equipement"=>$equipement]);
    }
    #[Route('/equipement/show_devis','show_devis')]
    public function show_devis(DevisRepository $repo)
    {
        $user=$this->getUser();
        if($user instanceof User){
            $id=$user->getId();
            $list=$repo->findBy(["fournisseur"=>$id]);
            return $this->render('/equipements/devis.html.twig', [
                'list' => $list,
            ]);
        }
        else
            return $this->redirectToRoute('app_login'); 
                
    }
}
