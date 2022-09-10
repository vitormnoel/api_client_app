<?php

namespace App\Controller\mobile;

use App\Controller\_common\Essentials;
use App\Entity\Department;
use App\Form\DepartmentLoginType;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Firebase\JWT\JWT;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/security')]
class SecurityController extends Essentials
{
    private ParameterBagInterface $parameterBag;

    public function __construct(EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        parent::__construct($entityManager);
        $this->parameterBag = $parameterBag;
    }

    #[Route('/login', name: 'public_department_login', methods: ['POST'])]
    public function login(Request $request, DepartmentRepository $departmentRepository, NativePasswordHasher $passwordHasher): JsonResponse
    {
        $response = $this->formSimple($request,DepartmentLoginType::class);
        if($response instanceof JsonResponse) {
            return $response;
        }

        $department =  $departmentRepository->findOneBy(['identifier' => $response['identifier']]);
        

        if(!$department) {
            return $this->response(false,null,'Access denied','Invalid Credentials');
        }

        if($passwordHasher->verify($department->getPassword(), $response['password'])){
            $jwt = $this->createJWT($department,$this->parameterBag->get('KEY_API'));
            $password  = $passwordHasher->hash( $response['password']);
            $department->setPassword($password);
            $this->getEntityManager()->flush();
            return $this->response(true,
            
                [
                    'token' => $jwt,
                    'idDepartment'=> $department->getId(),
                    'idFlow' => $department->getFlows()[0]->getId(),
                    
                ],'Created JWT successfully');
        }

        return $this->response(false,null,'Access denied','Invalid Credentials');
    }

    private function createJWT(Department $department, string $key): string
    {
        return JWT::encode(
            [
                'department' => $department
            ],
            $key,'HS256');
    }
}