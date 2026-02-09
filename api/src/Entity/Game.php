<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use App\Enum\GameStepEnum;
use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ApiResource(
    mercure: [
        'topics' => [
            '@=iri(object)',
        ],
    ],
    operations: [
        new Patch(name: 'api_game_update'),
    ],
)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: GameStepEnum::class)]
    private ?GameStepEnum $step = null;

    public function __construct()
    {
        $this->step = GameStepEnum::INITIALISATION;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStep(): ?GameStepEnum
    {
        return $this->step;
    }

    public function setStep(GameStepEnum $step): static
    {
        $this->step = $step;

        return $this;
    }
}
