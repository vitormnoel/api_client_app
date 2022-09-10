<?php

namespace App\Security\Authenticators;

use App\Entity\Department;
use App\Helper\RoutePermission;
use App\Helper\SecurityResources;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class DepartmentAuthenticator extends AbstractAuthenticator
{
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface $params;
    private ?Department $department = null;
    private DepartmentRepository $departmentRepository;


    public function __construct(
        EntityManagerInterface $entityManager,
        ParameterBagInterface $params, DepartmentRepository $departmentRepository)
    {
        $this->params = $params;
        $this->entityManager = $entityManager;
        $this->departmentRepository = $departmentRepository;
    }

    public function supports(Request $request): ?bool
    {
        if(str_contains($request->getHost(), 'api.') || str_contains($request->getHost(), '/api/')){
            $bearer = SecurityResources::getBearer($request);
            $access = $this->checkAccess($bearer);
            if($access) $this->department = $access;
            if(!RoutePermission::isPublicRoute($request->get('_route'))) return true;
            else if($access) return true;
            return false;
        }
        return false;
    }

    public function authenticate(Request $request): Passport
    {
        if(!$this->department) throw new CustomUserMessageAuthenticationException('No User provided');
        return new SelfValidatingPassport(new UserBadge($this->department->getIdentifier()));
    }

    private function checkAccess(?string $bearer): ?Department
    {
        if(!$bearer) return null;
        return $this->getCredentials($bearer);
    }

    private function getCredentials(string $bearer): ?Department
    {
        if(!$bearer) throw new CustomUserMessageAuthenticationException('No API token provided');
        $token = str_replace('Bearer ','',$bearer);
        try {
            $credentials = SecurityResources::decodeJWT($token,$this->params->get('KEY_API'));
        }catch (\Exception $e){
            return null;
        }
         return $this->validDepartment($credentials);
    }

    private function validDepartment($credentials)
    {
        return $this->departmentRepository->findOneBy(['identifier' => $credentials->department->identifier]);
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return  throw new AccessDeniedException();
    }
}
