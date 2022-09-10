<?php

namespace App\Helper;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Persistence\ObjectRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Ulid;

class SecurityResources
{
    static function createJWT(UserInterface $user, string $key): string
    {
        $date_time = new \DateTimeImmutable('NOW');
        return JWT::encode(
            [
                'email' => $user->getEmail(),
                'register' => $user->getId(),
                'created_at' => $date_time->format('Y-m-d H:i:s')
            ],
            $key,'HS256');
    }

    static function decodeJWT(string $jwt, string $key): object
    {
        return JWT::decode($jwt,new Key($key, 'HS256'));
    }

   static function getBearer(Request $request): ?string
   {
       $bearer = $request->headers->get('Authorization');
       if(!$bearer) return null;
       return str_replace('Bearer ','',$bearer);
   }

   static function expiredTime($credentials){
        $current_date_time = new \DateTime('NOW -2 hours');
        $date_time_credential = new \DateTime($credentials->created_at);
        return$current_date_time >=  $date_time_credential;

   }

   static function getCredentials(Request $request,string $key): ?object
   {
       $token = str_replace('Bearer ','',SecurityResources::getBearer($request));
       $credentials = SecurityResources::decodeJWT($token,$key);
       if (!is_object($credentials) || !property_exists($credentials, 'email') || !property_exists($credentials, 'register')) return null;
       return $credentials;
   }

    static function validUser($credentials,ObjectRepository | ServiceEntityRepositoryInterface $userRepository)
    {
        $user = $userRepository->getForEmail($credentials->email);
        return $user ?? null;
    }

    static function generateUlid(): string
    {
        $ulid = new Ulid();
        return $ulid->toBase58();
    }
}