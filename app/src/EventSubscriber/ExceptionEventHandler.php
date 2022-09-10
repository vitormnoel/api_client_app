<?php

namespace App\EventSubscriber;

use App\Factory\ResponseFactory;
use Doctrine\DBAL\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class ExceptionEventHandler implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents():array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['handle404Exception', 0],
                ['handle403Exception', 1],
                ['doctrineException', 2],
                ['handleGenericException',-1],
            ],
        ];
    }
    public function handle404Exception(ExceptionEvent $event)
    {
        if($event->getThrowable() instanceof NotFoundHttpException){
            $response = ResponseFactory::fromError($event
                ->getThrowable())->getResponse();
            $response->setStatusCode(404);
            $event->setResponse($response);
        }
    }

    public function handle403Exception(ExceptionEvent $event)
    {
        if($event->getThrowable() instanceof AccessDeniedException){
            $response = ResponseFactory::fromError($event
                ->getThrowable())->getResponse();
            $response->setStatusCode(403);
            $event->setResponse($response);
        }
    }

    public function doctrineException(ExceptionEvent $event)
    {
        if($event->getThrowable() instanceof Exception){
            $response = ResponseFactory::fromError(new \Exception('Serious Doctrine Error! Inform the Administrator'));
            $event->setResponse($response->getResponse());
        }
    }

    public function handleGenericException(ExceptionEvent $event)
    {
        $response = ResponseFactory::fromError($event->getThrowable());
        $response->addUser($this->security->getUser() ?? null);
        $event->setResponse($response->getResponse());
    }
}