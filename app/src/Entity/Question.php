<?php

namespace App\Entity;

use App\Attribute\QuestionAttribute;
use App\Interface\QuestionInterface;
use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: 'type_answer',type: 'string')]
#[ORM\DiscriminatorMap(QuestionAttribute::MAP)]
#[ORM\Cache(usage: "NONSTRICT_READ_WRITE")]
abstract class Question extends Entity implements QuestionInterface
{
    #[ORM\Column(type: Types::TEXT)]
    private ?string $enunciation = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Flow $flow = null;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Answer::class, orphanRemoval: true)]
    private Collection $answer;

    public function __construct()
    {
        parent::__construct();
        $this->answer = new ArrayCollection();
    }


    public function getEnunciation(): ?string
    {
        return $this->enunciation;
    }

    public function setEnunciation(string $enunciation): self
    {
        $this->enunciation = $enunciation;

        return $this;
    }

    public function getFlow(): ?Flow
    {
        return $this->flow;
    }

    public function setFlow(?Flow $flow): self
    {
        $this->flow = $flow;

        return $this;
    }

    public function getType(): string
    {
        return QuestionAttribute::getKeyParam(self::class);
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswer(): Collection
    {
        return $this->answer;
    }


    public function addAnswer(Answer $answer): self
    {
        if (!$this->answer->contains($answer)) {
            $this->answer[] = $answer;
            $answer->setQuestion($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answer->removeElement($answer) && $answer->getQuestion() === $this) {
            $answer->setQuestion(null);
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => QuestionAttribute::getKeyParam($this::class),
            'id' => $this->getId(),
            'flow' => $this->getFlow()->getId(),
            'enunciation' => $this->getEnunciation()
        ];
    }

}
