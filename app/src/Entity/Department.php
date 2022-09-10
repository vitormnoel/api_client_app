<?php

namespace App\Entity;

use App\Helper\StringResources;
use App\Repository\DepartmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
#[ORM\Cache(usage: "NONSTRICT_READ_WRITE")]
#[UniqueEntity(fields: ['name'], message: "Department with this Name Already Exists!")]
class Department extends Entity implements UserInterface
{
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'department', targetEntity: Flow::class, cascade: ['remove','persist'], orphanRemoval: true)]
    private Collection $flows;

    #[ORM\OneToMany(mappedBy: 'department', targetEntity: Session::class, orphanRemoval: true)]
    private Collection $sessions;

    #[ORM\Column(length: 100)]
    private ?string $identifier = null;

    #[ORM\Column(length: 100)]
    private ?string $password = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        parent::__construct();
        $this->flows = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->identifier =  StringResources::generateRandom();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        $this->setSlug(StringResources::turnIntoId($this->name));
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Flow>
     */
    public function getFlows(): Collection
    {
        return $this->flows;
    }

    public function addFlow(Flow $flow): self
    {
        if (!$this->flows->contains($flow)) {
            $this->flows[] = $flow;
            $flow->setDepartment($this);
        }

        return $this;
    }

    public function removeFlow(Flow $flow): self
    {
        if ($this->flows->removeElement($flow) && $flow->getDepartment() === $this) {
            $flow->setDepartment(null);
        }
        return $this;
    }


    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): self
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions[] = $session;
            $session->setDepartment($this);
        }

        return $this;
    }

    public function removeSession(Session $session): self
    {
        if ($this->sessions->removeElement($session) && $session->getDepartment() === $this) {
            $session->setDepartment(null);
        }

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' =>$this->getName(),
            'slug' => $this->getSlug(),
            'identifier' => $this->getIdentifier()
        ];
    }

    public function getRoles(): array
    {
        return ['ROLE_DEPARTMENT'];
    }

    public function eraseCredentials(){
        return null;
    }

    public function getUserIdentifier(): string
    {
        return $this->getIdentifier();
    }
}
