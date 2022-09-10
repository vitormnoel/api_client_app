<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission extends Entity
{

    #[ORM\Column]
    private ?bool $flow_create = false;

    #[ORM\Column]
    private ?bool $flow_delete = false;

    #[ORM\Column]
    private ?bool $flow_update = false;

    #[ORM\Column]
    private ?bool $flow_view = false;

    #[ORM\Column]
    private ?bool $report_view = false;

    #[ORM\Column]
    private ?bool $report_download = false;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Department $department = null;

    #[ORM\ManyToOne(inversedBy: 'permission')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;


    public function isFlowCreate(): ?bool
    {
        return $this->flow_create;
    }

    public function setFlowCreate(bool $flow_create): self
    {
        $this->flow_create = $flow_create;

        return $this;
    }

    public function isFlowDelete(): ?bool
    {
        return $this->flow_delete;
    }

    public function setFlowDelete(bool $flow_delete): self
    {
        $this->flow_delete = $flow_delete;

        return $this;
    }

    public function isFlowUpdate(): ?bool
    {
        return $this->flow_update;
    }

    public function setFlowUpdate(bool $flow_update): self
    {
        $this->flow_update = $flow_update;

        return $this;
    }

    public function isFlowView(): ?bool
    {
        return $this->flow_view;
    }

    public function setFlowView(bool $flow_view): self
    {
        $this->flow_view = $flow_view;

        return $this;
    }

    public function isReportView(): ?bool
    {
        return $this->report_view;
    }

    public function setReportView(bool $report_view): self
    {
        $this->report_view = $report_view;

        return $this;
    }

    public function isReportDownload(): ?bool
    {
        return $this->report_download;
    }

    public function setReportDownload(bool $report_download): self
    {
        $this->report_download = $report_download;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function jsonSerialize(): array
    {
        // TODO: Implement jsonSerialize() method.
        return [];
    }
}
