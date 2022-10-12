<?php

namespace App\Controller\admin;

use App\Controller\_common\Essentials;
use App\Helper\ReportHelper;
use App\Repository\DepartmentRepository;
use App\Repository\FlowRepository;
use App\Repository\SessionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report')]
class ReportController extends Essentials
{
    #[Route('/flow', name: 'private_admin_report_flow', methods: ['GET'])]
    public function flowList(FlowRepository $flowRepository): JsonResponse
    {
        $flows = $flowRepository->findAll();
        $data = [
            'flows' => $flows,
            'amount' => count($flows)
        ];
        return $this->response(true,$data,'successfully');
    }


    #[Route('/flow/{id}', name: 'private_admin_report_flow_questions', methods: ['GET'])]
    public function reportFlow(string $id, FlowRepository $flowRepository,Request $request): JsonResponse
    {
        $flow  = $flowRepository->find($id);
        if(!$flow) {
            throw new NotFoundHttpException('Flow not found');
        }
        $date_start = $request->query->get('date_start', null);
        $date_finish = $request->query->get('date_finish', null);

        $report = $flowRepository->report($flow,$date_start,$date_finish);
        $data = ReportHelper::calculateMetrics(ReportHelper::groupQuestion($report));
        return $this->response(true,$data,'successfully');
    }

    #[Route('/flow/{id}/weekday', name: 'private_admin_report_weekday_flow_questions', methods: ['GET'])]
    public function reportFlowWeekDay(string $id, FlowRepository $flowRepository,Request $request): JsonResponse
    {
        $data = [];
        $flow  = $flowRepository->find($id);
        if(!$flow) {
            throw new NotFoundHttpException('Flow not found');
        }
        $date_start = $request->query->get('date_start', null);
        $date_finish = $request->query->get('date_finish', null);

        $report = $flowRepository->reportWeekDay($flow,$date_start,$date_finish);
        $week = ReportHelper::groupWeekDay($report);

        foreach ($week  as $key => $day){
            $data[$key] = ReportHelper::calculateMetrics(ReportHelper::groupQuestion($day));
        }
        return $this->response(true,$data,'successfully');
    }

    #[Route('/flow/{id}/weekday/shift', name: 'private_admin_report_weekday_shift_flow_questions', methods: ['GET'])]
    public function reportFlowWeekDayShift(string $id, FlowRepository $flowRepository,Request $request): JsonResponse
    {
        $data = [];
        $flow  = $flowRepository->find($id);
        if(!$flow) {
            throw new NotFoundHttpException('Flow not found');
        }
        $date_start = $request->query->get('date_start', null);
        $date_finish = $request->query->get('date_finish', null);

        if($date_start){
            $data['range']['init'] = $date_start;
        }
        if($date_finish){
            $data['range']['end'] = $date_finish;
        }

        $report = $flowRepository->reportWeekDayShift($flow,$date_start,$date_finish);
        $rotation = ReportHelper::groupRotation($report);
        foreach ($rotation  as $keyShift => $shift){
            $week = ReportHelper::groupWeekDay($shift);
            foreach ($week  as $keyWeek => $day){
                $data['result'][$keyShift][$keyWeek] = ReportHelper::calculateMetrics(ReportHelper::groupQuestion($day));
            }
        }

        return $this->response(true,$data,'successfully');
    }

    #[Route('/department', name: 'private_admin_report_department', methods: ['GET'])]
    public function department(DepartmentRepository $departmentRepository): JsonResponse
    {
        $departments = $departmentRepository->findAll();
        $data = [
            'departments' => $departments,
            'amount' => count($departments)
        ];
        return $this->response(true,$data,'successfully');
    }


    #[Route('/session', name: 'private_admin_report_session', methods: ['GET'])]
    public function session(SessionRepository $sessionRepository): JsonResponse
    {
        $sessions = $sessionRepository->count([]);
        $data = [
            'amount' => $sessions
        ];
        return $this->response(true,$data,'successfully');
    }

}