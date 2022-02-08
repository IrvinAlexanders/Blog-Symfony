<?php

namespace App\Repository;

use App\Entity\BlogEntries;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogEntries|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogEntries|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogEntries[]    findAll()
 * @method BlogEntries[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogEntriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogEntries::class);
    }

    public function buscarEntradasBlog($user_id=false, $result=false)
    {
        $consulta = 'SELECT post.id, post.title, post.subtitle, post.text, post.image, post.date
                        , user.name user_name, user.id user_id
                    FROM App:BlogEntries post
                    JOIN post.user user ';

        if ($user_id !== false) {
            // var_dump('<pre>', );
            // exit;
            $consulta .= 'WHERE user.id = '.$user_id.' ';
        }

        $consulta .= 'ORDER BY post.date DESC, post.time DESC ';

        $Query = $this->getEntityManager()->createQuery(
            $consulta
        );
        return $result ? $Query->getResult() : $Query;
    }

    // /**
    //  * @return BlogEntries[] Returns an array of BlogEntries objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BlogEntries
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
