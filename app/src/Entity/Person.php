<?php

namespace App\Entity;

use App\Helper\StringResources;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\MappedSuperclass]
#[UniqueEntity(fields: ["cpf"],message: 'Cpf is already in use!')]
#[UniqueEntity(fields: ["cell_phone"],message: 'Phone is already in use!')]
abstract class Person extends Entity
{

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', unique: true,nullable: true)]
    private ?string $cpf = null;

    #[ORM\Column(type: 'string', unique: true,nullable: true)]
    private ?string $cell_phone = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(string $cpf): self
    {
        $this->cpf = $cpf;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCellPhone(): ?string
    {
        return $this->cell_phone;
    }

    /**
     * @param string|null $cell_phone
     * @return Person
     */
    public function setCellPhone(?string $cell_phone): Person
    {
        $this->cell_phone = StringResources::onlyDigits($cell_phone);
        return $this;
    }

}
