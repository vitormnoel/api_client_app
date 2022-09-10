<?php

namespace App\Entity;

use App\Attribute\QuestionAttribute;
use App\Repository\AswerOptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AswerOptionRepository::class)]
class QuestionOption extends Question
{

    #[ORM\Column]
    private ?bool $interaction;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $options = [];

    public function __construct()
    {
        parent::__construct();
        $this->interaction = false;

    }

    public function isInteraction(): ?bool
    {
        return $this->interaction;
    }

    public function setInteraction(bool $interaction): self
    {
        $this->interaction = $interaction;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(?array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function getType(): string
    {
        return QuestionAttribute::getKeyParam(self::class);
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(),[
            'interaction' => $this->isInteraction(),
            'options' => $this->getOptions()
        ]);
    }
}
