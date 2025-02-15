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
use App\Form\ModifierEquipementType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

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
    public function add(ManagerRegistry $doctrine,EquipementsRepository $repo,Request $request,#[Autowire('%photo_dir%')] string $photoDir ){
        $equipement=new Equipements();
        $em=$doctrine->getManager();
        $form=$this->createForm(AddEquipementsType::class,$equipement);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $equipement=$form->getData();
            if($photo=$form['image']->getData()){
                $filename=uniqid().'.'.$photo->guessExtension();
                $photo->move($photoDir,$filename); 
            }
            $equipement->setImage($filename);
            $em->persist($equipement);
            $em->flush();
            return $this->redirectToRoute('show_equipement_dashboard');
        }
        return $this->render("/equipements/add.html.twig",["form"=>$form]);
    }
    
    #[Route('/delete_equipement/{id}','delete_equipement')]
    public function delete(ManagerRegistry $doctrine,$id,EquipementsRepository $repo){
        $em=$doctrine->getManager();
        $prod=new Equipements();
        $prod=$repo->find($id);
        $em->remove($prod);
        $em->flush();
        return $this->redirectToRoute('show_equipement_dashboard');
    }
    #[Route('/modifier_equipement/{id}','modifier_equipement')]
    public function modifier($id,ManagerRegistry $doctrine,EquipementsRepository $repo,Request $request){
        $em=$doctrine->getManager();
        $equipement=new Equipements();
        $equipement=$repo->find($id);
        $form=$this->createForm(ModifierEquipementType::class,$equipement);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $form->getData();
            $em->persist($equipement);
            $em->flush();
            return $this->redirectToRoute('show_equipement_dashboard');
        }

        return $this->render('/equipements/modify.html.twig'
        ,["form"=>$form]);

    }
}
