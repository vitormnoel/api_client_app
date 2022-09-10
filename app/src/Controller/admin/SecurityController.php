<?php

namespace App\Controller\admin;

use App\Controller\_common\Essentials;
use App\Entity\TokenRecoveryAccess;
use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\LoginType;
use App\Form\ResetPasswordType;
use App\Helper\SecurityResources;
use App\Repository\TokenRecoveryAccessRepository;
use App\Repository\UserRepository;
use App\Service\UserRecovery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[Route('/security')]
class SecurityController extends Essentials
{
    private UserPasswordHasherInterface $passwordEncoder;
    private UserRepository $userRepository;
    private TokenRecoveryAccessRepository $tokenRecoveryAccessRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                UserPasswordHasherInterface $passwordEncoder,
                                UserRepository $userRepository,
                                TokenRecoveryAccessRepository $tokenRecoveryAccessRepository)
    {
        parent::__construct($entityManager);
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
        $this->tokenRecoveryAccessRepository = $tokenRecoveryAccessRepository;
    }

    #[Route('/login', name: 'public_admin_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $response = $this->formSimple($request,LoginType::class);
        if($response instanceof JsonResponse) {
            return $response;
        }

        $user = $this->userRepository->getForEmail($response['email']);

        if(!$user) {
            return $this->response(false,['message' => 'Invalid Credentials']);
        }
        $user = $this->authentication($user,$response['password']);

        if($user){
            $jwt = SecurityResources::createJWT($user, $this->getParameter('KEY_API'));
            $new_password = $this->passwordEncoder->hashPassword($user,$response['password']);
            $this->userRepository->upgradePassword($user,$new_password);
            return $this->response(true,
                [
                    'user' => $user,
                    'token' => $jwt
                ],'Created JWT successfully');
        }
        return $this->response(false,null,'permission denied', 'Invalid access data');
    }

    #[Route('/forgot-password', name: 'public_admin_forgot_password', methods: ['POST'])]
    public function forgotPassword(Request $request, UserRecovery $recovery): JsonResponse
    {
        $response = $this->formSimple($request,ForgotPasswordType::class);
        if(!is_array($response)){
            return $response;
        }
        $user = $this->userRepository->findOneBy(['email' => $response['email']]);
        if(!$user) return $this->response(false,null,'Not Found Email Address','Invalid Email');
        $token = $recovery->createTokenAccess($user);
        return $this->response(true,$token,'Successfully');
    }

    #[Route('/reset-password', name: 'public_admin_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        $token = $this->getTokenInRequest($request);
        if(!$token) return $this->response(false,null,'Token not Found','Invalid Token');
        if($this->validToken($token)) return $this->response(false,null,'Expired Token','Invalid Token');
        $request->request->remove('token');
        $response = $this->formSimple($request,ResetPasswordType::class);

        if(!is_array($response)){
            return $response;
        }

        /** @var User $user */
        $user = $token->getUser();
        $user->setPassword($this->passwordEncoder->hashPassword($user,$response['password']));
        $this->getEntityManager()->remove($token);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear(User::class);

        return $this->response(true,null,'Updated Password');
    }

    #[Route('/reset-password-token-info', name: 'public_admin_info_token', methods: ['GET'])]
    public function resetPasswordInfo(Request $request): JsonResponse
    {
        $token = $this->getTokenInRequest($request);
        if(!$token) return $this->response(false,['message' => 'Token not Found']);
        if($this->validToken($token)) return $this->response(false,null,'Expired Token','Invalid Token');
        return $this->response(true,$token,'Successfully');
    }

    public function authentication(PasswordAuthenticatedUserInterface $user, string $password): ?PasswordAuthenticatedUserInterface
    {
        return $this->passwordEncoder->isPasswordValid($user,$password) ? $user : null;
    }

    private function getTokenInRequest(Request $request): ?TokenRecoveryAccess
    {
        $request_token = $request->get('token') ?? null;
        if(!$request_token) throw new BadRequestHttpException('Token use mandatory');
        /** @var TokenRecoveryAccess $token */
        return  $this->tokenRecoveryAccessRepository->findOneBy(['token' => $request_token]) ?? null;
    }
    private function validToken($token): bool
    {
        $current_date = new \DateTime();
        return $current_date > $token->getExpiredAt() || $token->getStatus();
    }
}