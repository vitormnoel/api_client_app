<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session extends Entity
{

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Answer::class, orphanRemoval: true)]
    private Collection $answers;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Flow $flow = null;

    #[ORM\Column]
    private ?bool $ended = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $weekday = null;

    public function __construct()
    {
        parent::__construct();
        $this->answers = new ArrayCollection();
        $this->ended = false;
        $this->weekday = strtolower(date("D"));
    }

    /**
     * @return Collection<int, Answer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setSession($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer) && $answer->getSession() === $this) {
            $answer->setSession(null);
        }

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


    public function isEnded(): ?bool
    {
        return $this->ended;
    }

    public function setEnded(bool $ended): self
    {
        $this->ended = $ended;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'ended' => $this->isEnded(),
            'created_at' => $this->getCreatedAt(),
            'flow' => $this->flow,
            'department' => $this->flow->getDepartment()
        ];
    }

    public function getWeekday(): ?string
    {
        return $this->weekday;
    }

    public function setWeekday(?string $weekday): self
    {
        $this->weekday = $weekday;

        return $this;
    }
}
