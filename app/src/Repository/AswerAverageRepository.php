<?php

namespace App\Repository;

use App\Entity\QuestionAverage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionAverage>
 *
 * @method QuestionAverage|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionAverage|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionAverage[]    findAll()
 * @method QuestionAverage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AswerAverageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionAverage::class);
    }
}
