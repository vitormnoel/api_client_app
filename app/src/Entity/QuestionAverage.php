<?php

namespace App\Entity;

use App\Attribute\QuestionAttribute;
use App\Repository\AswerAverageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AswerAverageRepository::class)]
class QuestionAverage extends Question
{
    #[ORM\Column]
    private ?int $init_range = null;

    #[ORM\Column]
    private ?int $end_range = null;

    public function getInitRange(): ?int
    {
        return $this->init_range;
    }

    public function setInitRange(int $init_range): self
    {
        $this->init_range = $init_range;

        return $this;
    }

    public function getEndRange(): ?int
    {
        return $this->end_range;
    }

    public function setEndRange(int $end_range): self
    {
        $this->end_range = $end_range;

        return $this;
    }

    public function getType(): string
    {
        return QuestionAttribute::getKeyParam(self::class);
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(),[
            'init_range' => $this->getInitRange(),
            'end_range' => $this->getEndRange()
        ]);
    }
}
