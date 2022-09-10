<?php

namespace App\Controller\mobile;

use App\Controller\_common\Essentials;
use App\Entity\Department;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/department')]
class DepartmentController extends Essentials
{
    #[Route('/view', name: 'private_api_department_view', methods: ['GET'])]
    public function view(): JsonResponse
    {
        /** @var Department $department */
        $department = $this->getUser();
        if(!$department) {
            return $this->response(false,null,'access not allowed','Department not logged in');
        }
        return $this->response(true,['department' => $department],"Successfully");
    }

}