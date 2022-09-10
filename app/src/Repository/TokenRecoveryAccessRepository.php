<?php

namespace App\Repository;

use App\Entity\TokenRecoveryAccess;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TokenRecoveryAccess>
 *
 * @method TokenRecoveryAccess|null find($id, $lockMode = null, $lockVersion = null)
 * @method TokenRecoveryAccess|null findOneBy(array $criteria, array $orderBy = null)
 * @method TokenRecoveryAccess[]    findAll()
 * @method TokenRecoveryAccess[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenRecoveryAccessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenRecoveryAccess::class);
    }

    public function add(TokenRecoveryAccess $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TokenRecoveryAccess $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return TokenRecoveryAccess[] Returns an array of TokenRecoveryAccess objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TokenRecoveryAccess
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
