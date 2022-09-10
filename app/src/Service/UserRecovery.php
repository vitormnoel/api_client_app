<?php


namespace App\Service;

use App\Entity\TokenRecoveryAccess;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserRecovery
{
    private TokenGeneratorInterface $tokenGenerator;
    private EntityManagerInterface $entityManager;

    public function __construct(TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $entityManager)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->entityManager = $entityManager;
    }

    public function createTokenAccess(UserInterface $user): TokenRecoveryAccess
    {
        $this->removePreviousToken($user);
        $token = new TokenRecoveryAccess();
        $token->setUser($user);
        $token->setToken($this->tokenGenerator->generateToken());
        $this->entityManager->persist($token);
        $this->entityManager->flush();
        return $token;
    }

    private function removePreviousToken(UserInterface $user){
        $repository = $this->entityManager->getRepository(TokenRecoveryAccess::class);
        $token = $repository->findOneBy(['user' => $user]);
        if(!$token) return;
        $this->entityManager->remove($token);
        $this->entityManager->flush();
    }
}