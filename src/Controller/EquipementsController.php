<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\EquipementsRepository;
use App\Entity\Equipements;
use App\Form\AddEquipementsType;

final class EquipementsController extends AbstractController
{
    
    #[Route('/equipement/show_equipement' , 'show_equipement')]
    public function showProduit(ManagerRegistry $doctrine){
        $repo=$doctrine->getRepository(Equipements::class);
        $list=$repo->findAll();
        return $this->render( '/equipements/index.html.twig', [
            "list" =>$list
        ]);
    }

    #[Route('/equipement/show_equipement_dashboard' , 'show_equipement_dashboard')]
    public function show(ManagerRegistry $doctrine){
        $repo=$doctrine->getRepository(Equipements::class);
        $list=$repo->findAll();
        return $this->render( '/equipements/show.html.twig', [
            "list" =>$list
        ]);
    }
    
    #[Route('/add_equipement','add_equipement')]
    public function add(ManagerRegistry $doctrine,EquipementsRepository $repo,Request $request){
        $equipement=new Equipements();
        $em=$doctrine->getManager();
        $form=$this->createForm(AddEquipementsType::class,$equipement);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $form->getData();
            $em->persist($equipement);
            $em->flush();
        }
        return $this->render("/equipements/add.html.twig",["form"=>$form]);
    }
    
    #[Route('/delete_produit/{id}','delete_produit')]
    public function delete(ManagerRegistry $doctrine,$id,EquipementsRepository $repo){
        $em=$doctrine->getManager();
        $prod=new Equipements();
        $prod=$repo->find($id);
        $em->remove($prod);
        $em->flush();
        return $this->redirectToRoute('show_equipement_dashboard');
    }
}
