<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoryEquipementsRepository;
use App\Entity\CategoryEquipements;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\AddCategoryEquipementsType;


final class CategoryEquipementsController extends AbstractController
{

    #[Route('/show_category','show_category_equipement')]
    public function show_category(CategoryEquipementsRepository $repo): Response{
        $list=$repo->findAll();
        return $this->render('/category_equipements/index.html.twig',[
            "list"=>$list
        ]);
    }
    #[Route('/delete_categoryAgri/{id}','delete_categoryAgri')]
    public function delete($id,ManagerRegistry $doctrine,){
        $repo=$doctrine->getRepository(CategoryEquipements::class);
        $doc=$doctrine->getManager();
        $cate=new CategoryEquipements();
        $cate=$repo->find($id);
        $doc->remove($cate);
        $doc->flush();
        //return Response(200,"Suppression fite");
        return $this->redirectToRoute('show_category_equipement');
    }

    #[Route('/add_category','add_category_equipement')]
    public function add_category(ManagerRegistry $doctrine,Request $request){
        //$repo=$doctrine->getRepository(CategoryEquipements::class);
        $cat=new CategoryEquipements();
        $em=$doctrine->getManager();
        $form=$this->createForm(AddCategoryEquipementsType::class,$cat);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $form->getData();
            $em->persist($cat);
            $em->flush();
        }
        return $this->render("/category_equipements/add.html.twig",["form"=>$form->createView()]);
    

    }

    
}
