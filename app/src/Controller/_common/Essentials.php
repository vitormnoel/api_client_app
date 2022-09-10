<?php

namespace App\Controller\_common;

use App\Factory\ResponseFactory;
use App\Helper\FormResources;
use App\Interface\EntityInterface;
use App\Interface\Serializable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Essentials extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function formSimple(Request $request, string $type): array | JsonResponse
    {
        $form = $this->createForm($type);
        $content = $request->request->all();
        $form->submit($content);
        if(!$form->isValid()) return $this->response(false,null,"Invalid Parameters",$this->extractErrorForm($form));
        return $form->getData();
    }

    public function formForEntity(Request $request, string $type, $entity = null): Serializable | EntityInterface | JsonResponse
    {
        $form = $this->createForm($type,$entity);
        $content = $request->get($form->getName());
        $form->submit($content);
        if(!$form->isValid()) return $this->response(false,null,"Invalid Parameters",$this->extractErrorForm($form));
        $entity = $form->getData();
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $this->entityManager->clear(get_class($entity));
        return $entity;
    }


    // FUNCTIONS
    public function response($success = true,
                             $content = [],
                             $message = null,
                             $error = null,
                             int $code = null,
                             $page= null,
                             $itemsPerPage =null,
                             int $countAll = null
    ): JsonResponse
    {

        $response = new ResponseFactory($success,$content,$message,$error,$code ?? Response::HTTP_OK,$page,$itemsPerPage,$countAll);
        $response->addUser($this->getUser());
        return $response->getResponse();
    }

    private function extractErrorForm($form):array
    {
        $data = [
            'message' => 'Invalid data',
            'error' => FormResources::getErrors($form)
        ];
        if($form->getExtraData()){
            $data['extra_fields'] = $form->getExtraData();
        }
        return $data;
    }
}