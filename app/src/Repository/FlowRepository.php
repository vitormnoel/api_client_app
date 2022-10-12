<?php

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Flow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PDO;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @extends ServiceEntityRepository<Flow>
 *
 * @method Flow|null find($id, $lockMode = null, $lockVersion = null)
 * @method Flow|null findOneBy(array $criteria, array $orderBy = null)
 * @method Flow[]    findAll()
 * @method Flow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flow::class);
    }

    public function report(Flow $flow,$date_start = null, $date_finish = null)
    {
        $flow = $flow->getId();
        $valueJson = '$.value';
        $conn = $this->getEntityManager()->getConnection();

        $sqlAnswers = "SELECT answer.question_id, JSON_UNQUOTE(JSON_EXTRACT(answer.data, :jsonValue)) as reply, COUNT(*) as repeated  FROM answer ";

       if($date_start || $date_finish){
           try {
               $sqlAnswers .= "INNER JOIN session s ON answer.session_id = s.id WHERE true ";
               if ($date_start) {
                   $start = new \DateTime($date_start);
                   $string_date_start = $start->format('Y-m-d H:i');
                   $sqlAnswers .= " AND s.created_at >= '$string_date_start'";
               }
               if ($date_finish) {
                   $finish = new \DateTime($date_finish);
                   $sqlAnswers .= " AND s.created_at <= '".$finish->format('Y-m-d H:i')."'";
               }
           }catch (\Exception $e){
               return throw new BadRequestHttpException('Format Date Invalid');
           }
       }

        $sql = "SELECT q.id as question,q.type_answer as type,  q.enunciation ,a.reply,a.repeated FROM flow f
        INNER JOIN question q ON f.id  = q.flow_id 
        INNER JOIN ($sqlAnswers group by reply, question_id) a ON a.question_id = q.id
        where f.id = :flow        
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':flow',$flow,PDO::PARAM_STR);
        $stmt->bindParam(':jsonValue',$valueJson,PDO::PARAM_STR);
       return  $stmt->executeQuery()->fetchAllAssociative();
    }

    public function reportWeekDay(Flow $flow,$date_start = null, $date_finish = null)
    {
        $flow = $flow->getId();
        $valueJson = '$.value';
        $conn = $this->getEntityManager()->getConnection();

        $sqlAnswers = "SELECT answer.question_id, JSON_UNQUOTE(JSON_EXTRACT(answer.data, :jsonValue)) as reply, COUNT(*) as repeated, s.weekday FROM answer ";
        $sqlAnswers .= "INNER JOIN session s ON answer.session_id = s.id WHERE true ";
        if($date_start || $date_finish){
            try {
                if ($date_start) {
                    $start = new \DateTime($date_start);
                    $string_date_start = $start->format('Y-m-d H:i');
                    $sqlAnswers .= " AND s.created_at >= '$string_date_start'";
                }
                if ($date_finish) {
                    $finish = new \DateTime($date_finish);
                    $sqlAnswers .= " AND s.created_at <= '".$finish->format('Y-m-d H:i')."'";
                }
            }catch (\Exception $e){
                return throw new BadRequestHttpException('Format Date Invalid');
            }
        }

        $sql = "SELECT q.id as question,q.type_answer as type,  q.enunciation ,a.reply,a.repeated,a.weekday FROM flow f
        INNER JOIN question q ON f.id  = q.flow_id 
        INNER JOIN ($sqlAnswers group by reply, question_id, s.weekday) a ON a.question_id = q.id
        where f.id = :flow    
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':flow',$flow,PDO::PARAM_STR);
        $stmt->bindParam(':jsonValue',$valueJson,PDO::PARAM_STR);
        return  $stmt->executeQuery()->fetchAllAssociative();
    }

    public function reportWeekDayShift(Flow $flow,$date_start = null, $date_finish = null,$shift = null)
    {
        $flow = $flow->getId();
        $valueJson = '$.value';
        $conn = $this->getEntityManager()->getConnection();

        $sqlAnswers = "SELECT answer.question_id, JSON_UNQUOTE(JSON_EXTRACT(answer.data, :jsonValue)) as reply, COUNT(*) as repeated, s.weekday, CASE
    WHEN HOUR(s.created_at) > 01 AND HOUR(s.created_at) < 12 THEN 'morning'
    WHEN HOUR(s.created_at) > 12 AND HOUR(s.created_at) < 18 THEN 'afternoon'
    ELSE 'night' END as shift FROM answer ";
        $sqlAnswers .= "INNER JOIN session s ON answer.session_id = s.id WHERE true ";
        if($date_start || $date_finish){
            try {
                if ($date_start) {
                    $start = new \DateTime($date_start);
                    $string_date_start = $start->format('Y-m-d H:i');
                    $sqlAnswers .= " AND s.created_at >= '$string_date_start'";
                }
                if ($date_finish) {
                    $finish = new \DateTime($date_finish);
                    $sqlAnswers .= " AND s.created_at <= '".$finish->format('Y-m-d H:i')."'";
                }
            }catch (\Exception $e){
                return throw new BadRequestHttpException('Format Date Invalid');
            }
        }

        $sql = "SELECT q.id as question,q.type_answer as type,  q.enunciation ,a.reply,a.repeated,a.shift,a.weekday FROM flow f
        INNER JOIN question q ON f.id  = q.flow_id 
        INNER JOIN ($sqlAnswers group by reply, question_id,shift, s.weekday) a ON a.question_id = q.id
        where f.id = :flow    
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':flow',$flow,PDO::PARAM_STR);
        $stmt->bindParam(':jsonValue',$valueJson,PDO::PARAM_STR);
        return  $stmt->executeQuery()->fetchAllAssociative();
    }

}
