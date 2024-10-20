<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Form\AuthorSearchType;
use App\Repository\AuthorRepository;
use App\Repository\LibraryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/crud/author')]
class CrudAuthorController extends AbstractController
{
    #[Route('/list', name: 'app_crud_author')]
    public function list(Request $request, AuthorRepository $repository): Response
    {
        // Créer le formulaire de recherche
        $form = $this->createForm(AuthorSearchType::class);
        $form->handleRequest($request);

        // Récupérer tous les auteurs par défaut
        $list = $repository->findAll();

        // Si le formulaire est soumis et valide, filtrer les auteurs
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $minBooks = $data['minBooks'];
            $maxBooks = $data['maxBooks'];

            // Rechercher les auteurs avec le nombre de livres entre min et max
            $list = $repository->findByNumberOfBooks($minBooks, $maxBooks);
        }

        // Rendre la vue avec le formulaire et la liste des auteurs
        return $this->render('crud_author/list.html.twig', [
            'form' => $form->createView(),
            'list' => $list,
        ]);
    }

    #[Route("/search/{name}", name: 'app_crud_search')]
    public function searchByName(AuthorRepository $repository, Request $request): Response
    {
        $name = $request->get('name');
        //var_dump($name);
        //die();

        $authors = $repository->findByName($name);
        //var_dump($authors);
        //die();
        return $this->render(
            'crud_author/list.html.twig',
            ['list' => $authors]
        );
    }

    #[Route('/new', name: 'app_new_author')]
    public function newAuthor(ManagerRegistry $doctrine): Response
    {
        //Création d'un auteur avec des données statiques
        $author = new Author();
        $author->setName('Youssef');
        $author->setEmail('Malki@gmail.com');
        $author->setNbrBooks(3);
        $author->setAdress('Marsa');

        //Persister l'auteur
        $em = $doctrine->getManager();
        $em->persist($author);
        $em->flush();

        //rediriger vers la liste des auteurs
        return $this->redirectToRoute('app_crud_author');
    }

    //2ème méthode pour l'ajout statique en utilisant EntityManagerInterface au lieu de ManagerRegistry
    #[Route('/add-static', name: 'app_crud_author_add_static')]
    public function addStatic(EntityManagerInterface $entityManager): Response
    {
        //Création d'un auteur avec des données statiques
        $author = new Author();
        $author->setName('Youssef');
        $author->setEmail('Malki@gmail.com');
        $author->setNbrBooks(3);
        $author->setAdress('Marsa');

        //Persister l'auteur
        $entityManager->persist($author);
        $entityManager->flush();

        //rediriger vers la liste des auteurs
        return $this->redirectToRoute('app_crud_author');
    }

    #[Route('/add', name: 'app_crud_author_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        //Créer un nouvel auteur
        $author = new Author();

        //Créer le formulaire pour l'auteur
        $form = $this->createForm(AuthorType::class, $author);

        //Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le formulaire est soumis et valide, persister l'auteur en base de données

            $entityManager->persist($author);
            $entityManager->flush();

            //rediriger vers la liste des auteurs après l'ajout
            return $this->redirectToRoute('app_crud_author');
        }

        // Afficher le formulaire
        return $this->render('crud_author/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_crud_author_edit')]
    public function edit(Author $author, Request $request, EntityManagerInterface $entityManager): Response
    {
        //Créer le formulaire pré-rempli avec les données actuelles de l'auteur
        $form = $this->createForm(AuthorType::class, $author);

        //Gérer la requete (GET ou POST)
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Sauvegarder les modifications en bd 
            $entityManager->flush();

            //rediriger vers la liste des auteurs après la modification
            return $this->redirectToRoute('app_crud_author');
        }

        //Affiche le formulaire de modification
        return $this->render('crud_author/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    //2ème méthode pour Edit static 
    #[Route('/update/{id}', name: 'app_update_author')]
    public function update(Request $request, AuthorRepository $rep, ManagerRegistry $doctrine): Response
    {
        $id = $request->get('id');
        $author = $rep->find($id);
        $author->setEmail('badia@gmail.com');
        $em = $doctrine->getManager();
        $em->flush();
        return $this->redirectToRoute('app_crud_author');
    }


    #[Route('/delete/{id}', name: 'app_crud_author_delete')]
    public function delete(int $id, AuthorRepository $repository, EntityManagerInterface $entityManager): Response
    {
        //Récupérer l'auteur à supprimer par ID
        $author = $repository->find($id);

        //Vérifier si l'auteur existe
        if (!$author) {
            throw $this->createNotFoundException('No author found for id ' . $id);
        }

        //Suppression de l'auteur
        $entityManager->remove($author);
        $entityManager->flush();

        //Rediriger vers la liste des auteurs après la suppression
        return $this->redirectToRoute('app_crud_author');
    }


    //2ème méthode pour delete
    #[Route('/supprimer/{id}', name: 'app_delete_author')]
    public function supprimer(Author $author, ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $em->remove($author);
        $em->flush();
        return $this->redirectToRoute('app_crud_author');
    }

    
    //Cette fonction marche avant l'ajout de formulaire pour le nbr min et max des livres
    /*#[Route('/search', name: 'app_crud_author_search')]
    public function search(Request $request, AuthorRepository $repository): Response
    {
        $libraryName = $request->query->get('library_name'); // Obtenir le nom de la bibliothèque depuis la requête
    
        if ($libraryName) {
            // Si un nom de bibliothèque est fourni, recherchez les auteurs correspondants
            $authors = $repository->findByLibraryName($libraryName);
        } else {
            // Sinon, récupérer tous les auteurs
            $authors = $repository->findAll();
        }
    
        return $this->render('crud_author/list.html.twig', [
            'list' => $authors,
        ]);
    }*/

    #[Route('/search', name: 'app_crud_author_search')]
    public function search(Request $request, AuthorRepository $repository): Response
    {
        $libraryName = $request->query->get('library_name');

        // Créer le formulaire de recherche par nombre de livres
        $form = $this->createForm(AuthorSearchType::class); // Créez le formulaire comme dans la méthode list
        $form->handleRequest($request);

        $authors = $repository->findAll(); // Auteurs par défaut

        if ($libraryName) {
            // Si un nom de bibliothèque est fourni, rechercher les auteurs
            $authors = $repository->findByLibraryName($libraryName);
        }

        // Renvoyer le formulaire avec la vue pour éviter l'erreur "form" non défini
        return $this->render('crud_author/list.html.twig', [
            'list' => $authors,
            'form' => $form->createView(), // Passer la vue du formulaire
        ]);
    }


    #[Route('/delete-authors-with-no-books', name: 'app_crud_author_delete_no_books')]
    public function deleteAuthorsWithNoBooks(AuthorRepository $repository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les auteurs dont le nombre de livres est égal à zéro
        $authors = $repository->findBy(['nbrBooks' => 0]);

        foreach ($authors as $author) {
            // Supprimer l'auteur
            $entityManager->remove($author);
        }

        // Appliquer les changements
        $entityManager->flush();

        // Rediriger vers la liste des auteurs
        return $this->redirectToRoute('app_crud_author');
    }

    // supprimer auteur sans livre avec la méthode dql développée dans le repository et appelée dans le controller
    #[Route('/deletee/no-books', name: 'app_crud_author_supprimer_no_books')]
    public function deleteNoBooks(AuthorRepository $repository): Response
    {
        $repository->deleteAuthorsWithNoBooks(); // Appel de la méthode du repository

        return $this->redirectToRoute('app_crud_author');
    }

    // supprimer auteur sans livre avec la méthode dql dévéloppée directement dans le controller

    /*#[Route('/deletee/no-books', name: 'app_crud_author_supprimer_no_books')]
    public function deleteNoBooks(EntityManagerInterface $entityManager): Response
    {
        // DQL pour supprimer les auteurs dont le nombre de livres est égal à 0
        $dql = 'DELETE FROM App\Entity\Author a WHERE a.nbrBooks = 0';
        
        $query = $entityManager->createQuery($dql);
        $query->execute(); // Exécuter la requête

        return $this->redirectToRoute('app_crud_author'); // Rediriger vers la liste des auteurs
    }*/

    #[Route('/authors-by-email', name: 'app_crud_author_by_email')]
    public function listAuthorsByEmail(Request $request , AuthorRepository $repository): Response
    {
        // Appeler la méthode du repository pour récupérer les auteurs triés par email
        $authors = $repository->listAuthorByEmail();

        // Créer le formulaire de recherche par nombre de livres
        $form = $this->createForm(AuthorSearchType::class); // Créez le formulaire comme dans la méthode list
        $form->handleRequest($request);

        return $this->render('crud_author/list.html.twig', [
            'list' => $authors,
            'form' => $form->createView(), // Passer la vue du formulaire
        ]);
    }
}
