<?php

namespace App\Factory;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Throwable;

class ResponseFactory
{
    private bool $success;
    private mixed $data;
    private mixed $error;
    private string $message;
    private int $statusCode;
    private ?int $currentPage;
    private ?int $itemsPerPage;
    private ?int $countAll;
    private null|bool|UserInterface $user = false;

    public function __construct(
        bool $success,
        mixed $data,
        string $message = "",
        mixed $error = null,
        int $statusCode = 200,
        int $currentPage = null,
        int $itemsPerPage = null,
        int $countAll = null
    ){
        $this->success = $success;
        $this->data = $data;
        $this->message = $message;
        $this->error = $error;
        $this->statusCode = $statusCode;
        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
        $this->countAll = $countAll;
    }

    public function getResponse() : JsonResponse{
        $response = [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'error' => $this->error,
            'page' => $this->currentPage,
            'itemsForPage' => $this->itemsPerPage,
            'countAll' => $this->countAll,

        ];

        if(is_null($this->error)){
            unset($response['error']);
        }

        if(is_null($this->currentPage)){
            unset($response['page']);
            unset($response['itemsForPage']);
        }

        if(is_null($this->countAll)) {
            unset($response['countAll']);
        }

        $response['logged'] = (bool)$this->user;

        return new JsonResponse($response,$this->statusCode);
    }

    public static function fromError(Throwable $error, $msg = null): ResponseFactory
    {
        return new self(false,null,"unexpected error", $msg ?? $error->getMessage(), Response::HTTP_ACCEPTED);
    }

    public function addUser(?UserInterface $getUser)
    {
        $this->user = $getUser;
    }
}