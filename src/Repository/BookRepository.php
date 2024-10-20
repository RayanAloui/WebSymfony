<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    //Methode pour compter les livres d'une catégorie donnée
    // src/Repository/BookRepository.php

    public function countBooksByCategory($categoryName)
    {
        $dql = "SELECT COUNT(b.id) FROM App\Entity\Book b WHERE b.category = :category";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('category', $categoryName);

        return $query->getSingleScalarResult();
    }

    public function findPublishedBooksBetweenDates(\DateTime $startDate, \DateTime $endDate)
    {
        $dql = "SELECT b FROM App\Entity\Book b WHERE b.published = true AND b.publicationDate BETWEEN :startDate AND :endDate";

        $query = $this->getEntityManager()->createQuery($dql)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        return $query->getResult();
    }

    public function searchBookById(int $id)
    {
        return $this->createQueryBuilder('b')
            ->where('b.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function booksListByAuthors()
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.author', 'a')
            ->addSelect('a')
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBooksBefore2023()
    {
        // Étape 1 : Récupérer les IDs des auteurs avec plus de 10 livres
        $authors = $this->getEntityManager()
            ->getRepository(Author::class)
            ->createQueryBuilder('a')
            ->select('a.id')
            ->innerJoin('a.books', 'b') // Jointure avec la collection de livres
            ->where('a.nbrBooks > 10') // Utiliser l'attribut nbrBooks
            ->groupBy('a.id')
            ->getQuery()
            ->getResult();

        $authorIds = array_column($authors, 'id'); // Récupérer uniquement les IDs des auteurs

        // Étape 2 : Récupérer les livres de ces auteurs
        return $this->createQueryBuilder('b')
            ->where('b.published = true') // S'assurer que le livre est publié
            ->andWhere('b.publicationDate < :year') // Utiliser le champ publicationDate
            ->setParameter('year', new \DateTime('2023-01-01')) // Comparaison de date
            ->andWhere('b.author IN (:authorIds)')
            ->setParameter('authorIds', $authorIds)
            ->getQuery()
            ->getResult();
    }

    public function updateScienceFictionToRomance()
    {
        return $this->createQueryBuilder('b')
            ->update()
            ->set('b.category', ':newCategory')
            ->where('b.category = :oldCategory')
            ->setParameter('newCategory', 'Romance')
            ->setParameter('oldCategory', 'Science Fiction')
            ->getQuery()
            ->execute();
    }



    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
