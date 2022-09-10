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

    #[Route('/view/{identifier}', name: 'private_api_flow_view', methods: ['GET'])]
    public function view(string $identifier, FlowRepository $repository): JsonResponse
    {
        return $this->response(true,$this->getFlow($identifier,$repository),'successfully');
    }


    private function getFlow(string $identifier, FlowRepository $repository): Flow{
        $flow = $repository->find(trim($identifier));
        if(!$flow) {
            throw new NotFoundHttpException('Flow not found');
        }
        return $flow;
    }

}