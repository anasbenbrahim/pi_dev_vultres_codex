<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\EquipementsRepository;
use App\Entity\Equipements;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use App\Form\AddEquipementsType;
use App\Form\ModifierEquipementType;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class EquipementsController extends AbstractController
{      
    #[Route('/equipement/show_equipement' , name: 'show_equipement')]
    public function showProduit(ManagerRegistry $doctrine){
        $repo=$doctrine->getRepository(Equipements::class);
        $list=$repo->findAll();
        dump($list); // Debugging statement
    if (isset($list)) {
        return $this->render('/equipements/index.html.twig', [
            "list" => $list
        ]);
    }
}

    #[Route('/equipement/show_equipement_dashboard' , 'show_equipement_dashboard')]
    public function show(ManagerRegistry $doctrine,EquipementsRepository $repo){
        $fournisseur=$this->getUser();
        if(!$fournisseur){
            throw $this->createAccessDeniedException("makch connecte");
        }

        $repo=$doctrine->getRepository(Equipements::class);
        $list=$repo->findBy(['user'=>$fournisseur]);
        return $this->render( '/equipements/show.html.twig', [
            "list" =>$list
        ]);
    }
    #[Route('/show_detail/{id}','show_detail')]
    public function show_detail($id,EquipementsRepository $doctrine){
        $equipement=new equipements();
        $equipement=$doctrine->find($id);
        return $this->render('/equipements/show_detail.html.twig',
        ["equipement"=>$equipement]);
    }
    
    #[Route('/add_equipement','add_equipement')]
    public function add(ManagerRegistry $doctrine,EquipementsRepository $repo,Request $request,#[Autowire('%photo_dir%')] string $photoDir ){
        $equipement=new Equipements();
        $user=$this->getUser();
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
            $equipement->setUser($user);
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
    public function modifier($id,ManagerRegistry $doctrine,EquipementsRepository $repo,Request $request,#[Autowire('%photo_dir%')] string $photoDir){

    $em = $doctrine->getManager();
    $equipement = $repo->find($id);

    if (!$equipement) {
        throw $this->createNotFoundException("Équipement non trouvé.");
    }

    $ancienneImage = $equipement->getImage(); // Sauvegarde l'ancienne image avant modification

    $form = $this->createForm(ModifierEquipementType::class, $equipement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $photo = $form->get('image')->getData();

        if ($photo) { // Si une nouvelle image est envoyée
            $filename = uniqid() . '.' . $photo->guessExtension();
            $photo->move($photoDir, $filename);
            $equipement->setImage($filename);
        } else { // Sinon, garder l'ancienne image
            $equipement->setImage($ancienneImage);
        }

        $em->persist($equipement);
        $em->flush();

        return $this->redirectToRoute('show_equipement_dashboard');
    }

    return $this->render('/equipements/modify.html.twig', [
        'form' => $form->createView()
    ]);


    }

    #[Route('/add_equipement','add_equipement_fournisseur')]
    public function addequipement(ManagerRegistry $doctrine,EquipementsRepository $repo,Request $request,#[Autowire('%photo_dir%')] string $photoDir ){
        $fournisseur = $this->getUser();
        
        if (!$fournisseur) {
            throw $this->createAccessDeniedException("Accès refusé. Vous devez être un fournisseur.");
        }
        
        $equipement = new Equipements();
        $equipement->setUser($fournisseur);
        
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
}
