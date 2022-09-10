<?php

namespace App\Security\Authenticators;

use App\Entity\User;
use App\Helper\RoutePermission;
use App\Helper\SecurityResources;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class UserAuthenticator extends AbstractAuthenticator
{
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface $params;
    private ?UserInterface $user = null;


    public function __construct(
        EntityManagerInterface $entityManager,
        ParameterBagInterface $params)
    {
        $this->params = $params;
        $this->entityManager = $entityManager;
    }

    public function supports(Request $request): ?bool
    {
        if(str_contains($request->getHost(), 'admin.') || str_contains($request->getHost(), '/admin/') ){
            $bearer = SecurityResources::getBearer($request);
            $access = $this->checkAccess($bearer);
            if($access) $this->user = $access;
            if(!RoutePermission::isPublicRoute($request->get('_route'))) return true;
            else if($access) return true;
            return false;
        }
        return false;
    }

    public function authenticate(Request $request): Passport
    {
        if(!$this->user) throw new CustomUserMessageAuthenticationException('No User provided');
        if(!$this->user->isStatus()) throw new CustomUserMessageAuthenticationException('Access Denied');
        return new SelfValidatingPassport(new UserBadge($this->user->getEmail()));
    }

    private function checkAccess(?string $bearer): ?User
    {
        if(!$bearer) return null;
        return $this->getCredentials($bearer);
    }

    private function getCredentials(string $bearer): ?User
    {
        if(!$bearer) throw new CustomUserMessageAuthenticationException('No API token provided');
        $token = str_replace('Bearer ','',$bearer);
        try {
            $credentials = SecurityResources::decodeJWT($token,$this->params->get('KEY_API'));
        }catch (\Exception $e){
            return null;
        }
        if(SecurityResources::expiredTime($credentials)) return null;
        return SecurityResources::validUser($credentials,$this->entityManager->getRepository(User::class));
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
