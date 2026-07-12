<?php

namespace App\Entity\Game\Period;

use App\Entity\Game\Period\Action\SetupAction;
use App\Entity\Game\Period\Interface\PeriodInterface;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Game\Period\SetupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SetupRepository::class)]
class Setup implements PeriodInterface
{
    use UuidTrait;
    use TimestampableTrait;

    /** @var Collection<int, SetupAction> */
    #[ORM\OneToMany(targetEntity: SetupAction::class, mappedBy: 'setup', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $actions;

    public function __construct()
    {
        $this->actions = new ArrayCollection();
    }

    /**
     * @return Collection<int, SetupAction>
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(SetupAction $action): static
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
        }

        return $this;
    }
}
