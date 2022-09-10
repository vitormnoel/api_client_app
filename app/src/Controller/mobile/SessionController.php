<?php

namespace App\Controller\mobile;

use App\Controller\_common\Essentials;
use App\Entity\Session;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/session')]
class SessionController extends Essentials
{
    #[Route('/create', name: 'private_session_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $session = new Session();
        $response = $this->formForEntity($request,SessionType::class,$session);
        if(!$response instanceof Session){
            return $response;
        }
        return $this->response(true,$response,'Successfully');
    }

    // #[Route('/finish/{id}', name: 'private_session_finish', methods: ['PUT'])]
    #[Route('/finish/{identifier}', name: 'private_session_finish', methods: ['PUT'])]
    public function finish(string $identifier, SessionRepository $repository): JsonResponse
    {
        $session = $repository->find(trim($identifier));
        if(!$session){
            throw new NotFoundHttpException('Session not found');
        }
        $session->setEnded(true);
        $this->getEntityManager()->flush();
        return $this->response(true,$session,'Successfully');
    }
}