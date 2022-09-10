<?php

namespace App\Repository;

use App\Entity\QuestionBoolean;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionBoolean>
 *
 * @method QuestionBoolean|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionBoolean|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionBoolean[]    findAll()
 * @method QuestionBoolean[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AswerBooleanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionBoolean::class);
    }
}
