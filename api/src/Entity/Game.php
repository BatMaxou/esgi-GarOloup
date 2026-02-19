<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Model\Game\CreateGameOutput;
use App\Domain\Command\Game\Initialisation\CreateGameCommand;
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
        new Post(
            name: 'api_game_create',
            messenger: 'input',
            input: CreateGameCommand::class,
            output: CreateGameOutput::class,
        ),
        new Patch(name: 'api_game_update'),
    ],
)]
class Game
{
    use UuidTrait;

    #[ORM\Column(enumType: GameStepEnum::class)]
    private GameStepEnum $step;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $host = null;

    #[ORM\Column(length: 8)]
    private ?string $joinCode = null;

    public function __construct(
        Player $host,
    ) {
        $this->step = GameStepEnum::NEW;
        $this->host = $host;

        $uuid = $this->generateUuid();
        $this->id = $uuid;
        $this->joinCode = substr($uuid->toBase32(), -8);
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

    public function getHost(): ?Player
    {
        return $this->host;
    }

    public function setHost(Player $host): static
    {
        $this->host = $host;

        return $this;
    }

    public function getJoinCode(): ?string
    {
        return $this->joinCode;
    }

    public function setJoinCode(string $joinCode): static
    {
        $this->joinCode = $joinCode;

        return $this;
    }
}
