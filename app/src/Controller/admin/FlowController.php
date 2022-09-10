<?php

namespace App\Controller\admin;

use App\Controller\_common\Essentials;
use App\Entity\Flow;
use App\Form\FlowType;
use App\Repository\FlowRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/flow')]
class FlowController extends Essentials
{

    #[Route('/create', name: 'private_admin_flow_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return $this->factory(new Flow(),$request);
    }

    #[Route('/list/{department}', name: 'private_admin_flow_list', methods: ['GET'])]
    public function list(string $department, FlowRepository $repository): JsonResponse
    {
        return $this->response(true,$repository->findBy(['department' => trim($department)]),'List all departments');
    }

    #[Route('/view/{id}', name: 'private_admin_flow_view', methods: ['GET'])]
    public function view(string $id, FlowRepository $repository): JsonResponse
    {
        return $this->response(true,$this->getFlow($id,$repository),'successfully');
    }

    #[Route('/edit/{id}', name: 'private_admin_flow_edit', methods: ['POST'])]
    public function edit(string $id, Request $request, FlowRepository $repository): JsonResponse
    {
        return $this->factory($this->getFlow($id,$repository),$request);
    }

    #[Route('/delete/{id}', name: 'private_admin_flow_delete', methods: ['DELETE'])]
    public function delete(string $id, FlowRepository $repository): JsonResponse
    {
        $flow = $this->getFlow($id,$repository);
        $this->getEntityManager()->remove($flow);
        $this->getEntityManager()->flush();
        return $this->response(true,$flow,'successfully');
    }

    private function getFlow(string $id, FlowRepository $repository): Flow{
        $flow = $repository->find(trim($id));
        if(!$flow) {
            throw new NotFoundHttpException('Flow not found');
        }
        return $flow;
    }

    private function factory(Flow $flow,Request $request): JsonResponse
    {
        $response = $this->formForEntity($request,FlowType::class,$flow);
        if(!$response instanceof Flow){
            return $response;
        }
        return $this->response(true,$response,'successfully');
    }
}