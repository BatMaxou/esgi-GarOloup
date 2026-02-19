<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Uuid\UuidTrait;
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
        new Get(name: 'api_game_get'),
        new Post(name: 'api_game_create'),
        new Patch(name: 'api_game_update'),
    ],
)]
class Game
{
    use UuidTrait;

    #[ORM\Column(enumType: GameStepEnum::class)]
    private GameStepEnum $step;

    public function __construct()
    {
        $this->step = GameStepEnum::INITIALISATION;
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
