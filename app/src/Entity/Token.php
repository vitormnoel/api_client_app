<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\Cache(usage: "NONSTRICT_READ_WRITE")]
abstract class Token extends Entity
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $expired_at;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $token = null;

    #[ORM\Column(type: 'boolean')]
    private $status;

    public function __construct()
    {
        parent::__construct();
        $this->created_at = new \DateTime();
        $this->expired_at =  new \DateTime();
        $this->expired_at->add(new \DateInterval('PT2H'));
        $this->status = false;
    }

    public function getExpiredAt(): ?\DateTimeInterface
    {
        return $this->expired_at;
    }

    public function setDateExpiredAt(\DateTimeInterface $expired_at): self
    {
        $this->expired_at = $expired_at;

        return $this;
    }


    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'token' => $this->getToken(),
            'status' => $this->getStatus(),
            'date_created' => $this->getCreatedAt(),
            'date_expired' => $this->getExpiredAt()
        ];
    }
}
