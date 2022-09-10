<?php

namespace App\Controller\mobile;

use App\Controller\_common\Essentials;
use App\Entity\Flow;

use App\Repository\FlowRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/flow')]
class FlowController extends Essentials
{


    #[Route('/list/{department}', name: 'private_api_flow_list', methods: ['GET'])]
    public function list(string $department, FlowRepository $repository): JsonResponse
    {
        return $this->response(true,$repository->findBy(['department' => trim($department)]),'List all flow');
    }

    #[Route('/view/{id}', name: 'private_api_flow_view', methods: ['GET'])]
    public function view(string $id, FlowRepository $repository): JsonResponse
    {
        return $this->response(true,$this->getFlow($id,$repository),'successfully');
    }


    private function getFlow(string $id, FlowRepository $repository): Flow{
        $flow = $repository->find(trim($id));
        if(!$flow) {
            throw new NotFoundHttpException('Flow not found');
        }
        return $flow;
    }

}