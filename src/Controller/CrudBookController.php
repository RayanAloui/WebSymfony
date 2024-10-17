<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

#[Route('/crud/book')]
class CrudBookController extends AbstractController
{
    /*#[Route('/list', name: 'app_crud_book')]
    public function list(BookRepository $repository): Response
    {
        //récupération des données
        $list=$repository->findAll();
        return $this->render('crud_book/list.html.twig',
        ['list' =>$list]);
    }*/

    #[Route('/list', name: 'app_crud_book')]
    public function list(BookRepository $repository): Response
    {
        // Récupérer tous les livres publiés
        $publishedBooks = $repository->findBy(['published' => true]);

        // Compter le nombre de livres publiés et non publiés
        $totalBooks = $repository->count([]);
        $publishedCount = count($publishedBooks);
        $unpublishedCount = $totalBooks - $publishedCount;

        // Compter le nombre de livres dans la catégorie "Romance"
        $romanceCount = $repository->countBooksByCategory('Romance');

        return $this->render('crud_book/list.html.twig', [
            'publishedBooks' => $publishedBooks,
            'publishedCount' => $publishedCount,
            'unpublishedCount' => $unpublishedCount,
            'romanceCount' => $romanceCount,
        ]);
    }

    #[Route('/add', name: 'app_crud_book_add')]
    public function add(Request $request, AuthorRepository $authorRepository, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $book->setPublished(true); // Initialiser à true

        $form = $this->createForm(BookType::class, $book);

        // Remplir les choix d'auteurs
        $authors = $authorRepository->findAll();
        $authorChoices = [];
        foreach ($authors as $author) {
            $authorChoices[$author->getName()] = $author; // Assurez-vous que getName() renvoie le nom de l'auteur
        }

        $form->add('author', ChoiceType::class, [
            'label' => 'Author',
            'choices' => $authorChoices,
            'placeholder' => 'Select an author',
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'auteur associé au livre
            $author = $book->getAuthor();

            // Incrémenter l'attribut nb_books de l'auteur
            if ($author) {
                $author->setNbrBooks($author->getNbrBooks() + 1);
            }

            // Enregistrer le livre dans la base de données
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('app_crud_book');
        }

        return $this->render('crud_book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_crud_book_edit')]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        // Créer le formulaire en utilisant le BookType et l'entité Book existante
        $form = $this->createForm(BookType::class, $book);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les modifications dans la base de données
            $entityManager->flush();

            // Rediriger vers la liste des livres après la modification
            return $this->redirectToRoute('app_crud_book');
        }

        // Rendre la vue avec le formulaire
        return $this->render('crud_book/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_crud_book_delete')]
    public function delete(int $id, BookRepository $repository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le livre à supprimer par ID
        $book = $repository->find($id);

        // Vérifier si le livre existe
        if (!$book) {
            throw $this->createNotFoundException('No book found for id ' . $id);
        }

        // Suppression du livre
        $entityManager->remove($book);
        $entityManager->flush();

        // Rediriger vers la liste des livres après la suppression
        return $this->redirectToRoute('app_crud_book');
    }

    #[Route('/show/{id}', name: 'app_crud_book_show')]
    public function show(int $id, BookRepository $repository): Response
    {
        // Récupérer le livre par ID
        $book = $repository->find($id);

        // Vérifier si le livre existe
        if (!$book) {
            throw $this->createNotFoundException('No book found for id ' . $id);
        }

        // Afficher les détails du livre
        return $this->render('crud_book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/published-between', name: 'app_crud_book_between_dates')]
    public function publishedBetweenDates(BookRepository $repository): Response
    {
        // Définir les dates de début et de fin
        $startDate = new \DateTime('2014-01-01');
        $endDate = new \DateTime('2018-12-31');

        // Appeler la méthode du repository pour obtenir les livres publiés entre les dates
        $books = $repository->findPublishedBooksBetweenDates($startDate, $endDate);

        return $this->render('crud_book/published_between.html.twig', [
            'books' => $books,
        ]);
    }
}
