<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function findByLibraryName(string $libraryName)
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.library', 'l')
            ->where('l.name = :libraryName')
            ->setParameter('libraryName', $libraryName)
            ->getQuery()
            ->getResult();
    }

    public function findByNumberOfBooks(?int $min, ?int $max)
    {
        $dql = "SELECT a FROM App\Entity\Author a WHERE a.nbrBooks >= :min AND a.nbrBooks <= :max";
        $query = $this->getEntityManager()->createQuery($dql)
            ->setParameter('min', $min)
            ->setParameter('max', $max);

        return $query->getResult();
    }

    public function deleteAuthorsWithNoBooks(): void
    {
        $dql = 'DELETE FROM App\Entity\Author a WHERE a.nbrBooks = 0';
        $this->getEntityManager()->createQuery($dql)->execute();
    }

    //    /**
    //     * @return Author[] Returns an array of Author objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Author
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
