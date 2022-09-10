<?php

namespace App\Entity;

use App\Attribute\QuestionAttribute;
use App\Repository\AswerBooleanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AswerBooleanRepository::class)]
class QuestionBoolean extends Question
{
    public function getType(): string
    {
        return QuestionAttribute::getKeyParam(self::class);
    }
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize());
    }
}
