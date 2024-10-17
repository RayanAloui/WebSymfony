<?php

namespace App\Repository;

use App\Entity\Book;
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
