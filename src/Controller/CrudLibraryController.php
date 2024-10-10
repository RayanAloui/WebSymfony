<?php

namespace App\Controller;

use App\Entity\Library;
use App\Form\LibraryType;
use App\Repository\LibraryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/crud/library')]
class CrudLibraryController extends AbstractController
{
    #[Route('/list', name: 'app_crud_library')]
    public function list(LibraryRepository $repository): Response
    {
        //Récupération des données 
        $list=$repository->findAll();
        return $this->render('crud_library/list.html.twig',
        ['list' =>$list]);
    }

    #[Route('/add', name: 'app_crud_libray_add')]
    public function add(Request $request,EntityManagerInterface $entityManager): Response
    { 
        //Créer un nouvel library
        $library = new Library();

        //Créer le formulaire pour l'auteur
        $form = $this->createForm(LibraryType::class, $library);

        //Gérer la soumission du formulaire
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // Si le formulaire est soumis et valide, persister l'auteur en base de données
            $entityManager->persist($library);
            $entityManager->flush();

            //rediriger vers la liste des auteurs après l'ajout
            return $this->redirectToRoute('app_crud_library');
        }

        // Afficher le formulaire
        return $this->render('crud_library/add.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_crud_library_edit')]
    public function edit(Library $author, Request $request, EntityManagerInterface $entityManager): Response
    {
        //Créer le formulaire pré-rempli avec les données actuelles de l'auteur
        $form = $this->createForm(LibraryType::class, $author);

        //Gérer la requete (GET ou POST)
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //Sauvegarder les modifications en bd 
            $entityManager->flush();

            //rediriger vers la liste des auteurs après la modification
            return $this->redirectToRoute('app_crud_library');
        }

        //Affiche le formulaire de modification
        return $this->render('crud_library/edit.html.twig', [
               'form' => $form->createView(),
        ]);
    }


    #[Route('/delete/{id}', name: 'app_crud_library_delete')]
    public function delete(int $id,LibraryRepository $repository,EntityManagerInterface $entityManager): Response
    {
        //Récupérer l'auteur à supprimer par ID
        $library = $repository->find($id);

        //Vérifier si l'auteur existe
        if(!$library){
            throw $this->createNotFoundException('No library found for id '.$id);
        }

        //Suppression de l'auteur
        $entityManager->remove($library);
        $entityManager->flush();

        //Rediriger vers la liste des auteurs après la suppression
        return $this->redirectToRoute('app_crud_library');
    }


    #[Route("/search/{name}",name:'app_crud_recherche')]
    public function searchByName(LibraryRepository $repository, Request $request): Response
    {
         $name = $request->get('name');
         //var_dump($name);
         //die();

         $library=$repository->findByName($name);
         //var_dump($authors);
         //die();
         return $this->render('crud_library/list.html.twig',
           ['list' => $library]);
    }
}
