<?php

namespace App\Repository;

use App\Data\PostSearch;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @return Post[] Returns an array of Post objects
     */

    public function getRecent($arg): array
    {
        return $this->createQueryBuilder('element')
            ->orderBy('element.createdAt', 'DESC')
            ->setMaxResults($arg)
            ->getQuery()
            ->getResult();
    }

    public function getByGameId($gameId): array
    {
        return $this->createQueryBuilder('element')
            ->orderBy('element.createdAt', 'DESC')
            ->where('element.game ='.$gameId)
            ->getQuery()
            ->getResult();
    }

// Récupère les Posts en lien avec une Recherche

    public function findSearchQuery(PostSearch $postSearch): Query
    {
        $query = $this
            ->createQueryBuilder('p')
            ->join('p.game', 'g')
            ->join('p.author', 'a')
            ->select('g', 'p')
            ->orderBy('p.createdAt', 'DESC');

        // Filtre par titre
        if (!empty($postSearch->getTitle())) {
            $query = $query
                ->andWhere('p.title LIKE :title')
                ->setParameter('title', "%{$postSearch->getTitle()}%");
        }
        // Filtre par jeu
        if (!empty($postSearch->getGames())) {
            $query = $query
                ->andWhere('g.id IN (:games)')
                ->setParameter('games', $postSearch->getGames());
        }
        //Filtre par date
        if (!empty($postSearch->getMaxDate())) {
            $query = $query
                ->andWhere('p.createdAt <= :maxDate')
                ->setParameter('maxDate', $postSearch->getMaxDate()->format('Y-m-d') . ' 23:59:59');
        }
        if (!empty($postSearch->getMinDate())) {
            $query = $query
                ->andWhere('p.createdAt >= :minDate ')
                ->setParameter('minDate', $postSearch->getMinDate()->format('Y-m-d') . ' 00:00:00');
        }
        // Filtre par auteur
        if (!empty($postSearch->GetAuthor())) {
            $query = $query
                ->andWhere('a.username LIKE :author')
                ->setParameter('author', "%{$postSearch->getAuthor()}%");
        }
        return $query->getQuery();
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
