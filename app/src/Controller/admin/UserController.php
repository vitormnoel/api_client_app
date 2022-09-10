<?php

namespace App\Controller\admin;

use App\Controller\_common\Essentials;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends Essentials
{
    #[Route('/create', name: 'private_admin_user_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        return $this->factory(new User,$request);
    }

    #[Route('/edit', name: 'private_admin_user_edit', methods: ['POST'])]
    public function edit(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if(!$user) {
            return $this->response(false,null,'access not allowed','User not logged in');
        }
        return $this->factory($user,$request);
    }

    #[Route('/view', name: 'private_admin_user_view', methods: ['GET'])]
    public function view(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if(!$user) {
            return $this->response(false,null,'access not allowed','User not logged in');
        }
        return $this->response(true,['user' => $user],"Successfully");
    }

    private function factory(User $user, Request $request): JsonResponse
    {
        $response = $this->formForEntity($request,UserType::class,$user);
        if(!$response instanceof User){
            return $response;
        }
        return $this->response(true,$response,'Successfully');
    }
}