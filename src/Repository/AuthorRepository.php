<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry                  $registry,
        protected EntityManagerInterface $entityManager
    )
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * @param Author $author
     * @return void
     */
    public function persistByObjAuthor(Author $author): void
    {
        $this->entityManager->persist($author);
        $this->entityManager->flush();
    }

    /**
     * @param Author $author
     * @return void
     */
    public function removeByObjAuthor(Author $author): void
    {
        $this->entityManager->remove($author);
        $this->entityManager->flush();
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
