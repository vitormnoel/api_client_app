<?php

namespace App\Service;



use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionService
{


    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    public function putSession(string $name, mixed $content){
        $this->session->set($name, $content);
    }

    public function getSession(string $name){
        return $this->session->get($name) ?? [];
    }
    public function delete(string $name){
        return $this->session->remove($name);
    }
}