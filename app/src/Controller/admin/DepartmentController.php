<?php

namespace App\Controller\admin;

use App\Controller\_common\Essentials;
use App\Entity\Department;
use App\Form\ChangePasswordType;
use App\Form\DepartmentType;
use App\Repository\DepartmentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/department')]
class DepartmentController extends Essentials
{
    #[Route('/create', name: 'private_admin_department_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return $this->factory(new Department(),$request);
    }

    #[Route('/list', name: 'private_admin_department_list', methods: ['GET'])]
    public function list(DepartmentRepository $repository): JsonResponse
    {
        return $this->response(true,$repository->findAll(),'List all departments');
    }

    #[Route('/view/{id}', name: 'private_admin_department_view', methods: ['GET'])]
    public function view(string $id, DepartmentRepository $repository): JsonResponse
    {
        return $this->response(true,$this->getDepartment($id,$repository),'successfully');
    }

    #[Route('/edit/{id}', name: 'private_admin_department_edit', methods: ['POST'])]
    public function edit(string $id, Request $request, DepartmentRepository $repository): JsonResponse
    {
        return $this->factory($this->getDepartment($id,$repository),$request);
    }

    #[Route('/password/{id}', name: 'private_admin_department_change_password', methods: ['POST'])]
    public function password(string $id,Request $request,DepartmentRepository $repository,NativePasswordHasher $passwordEncoder): JsonResponse
    {
        $department = $repository->find(trim($id));
        if(!$department) {
            throw new NotFoundHttpException('Department not found');
        }
        $response = $this->formSimple($request,ChangePasswordType::class);
        if(!is_array($response)){
            return $response;
        }
        $department->setPassword($passwordEncoder->hash($response['password']));
        $this->getEntityManager()->flush();
        return $this->response(true,$response,'Successfully');
    }

    #[Route('/delete/{id}', name: 'private_admin_department_delete', methods: ['DELETE'])]
    public function delete(string $id, DepartmentRepository $repository): JsonResponse
    {
        $department = $this->getDepartment($id,$repository);
        $this->getEntityManager()->remove($department);
        $this->getEntityManager()->flush();

        return $this->response(true,$department,'successfully');
    }

    private function getDepartment(string $id, DepartmentRepository $repository): Department{
        $department = $repository->find(trim($id));
        if(!$department) {
            throw new NotFoundHttpException('Department not found');
        }
        return $department;
    }

    private function factory(Department $department,Request $request): JsonResponse
    {
        $response = $this->formForEntity($request,DepartmentType::class,$department);
        if(!$response instanceof Department){
            return $response;
        }
        return $this->response(true,$response,'Successfully');
    }
}