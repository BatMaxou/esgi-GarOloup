<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;
use App\Enum\RoleEnum;
use App\Repository\RoleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Attribute as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            name: 'api_get_role',
            normalizationContext: [
                'groups' => 'role:read',
            ],
        ),
        new GetCollection(
            name: 'api_get_roles',
            normalizationContext: [
                'groups' => 'role:list',
            ],
        ),
        new Patch(
            name: 'api_patch_role',
            security: 'is_granted("'.RoleEnum::ADMIN->value.'")',
            normalizationContext: [
                'groups' => 'role:read',
            ],
            denormalizationContext: [
                'groups' => 'role:write',
            ],
        ),
        new Post(
            name: 'api_put_role_files',
            uriTemplate: '/roles/{id}/files',
            inputFormats: ['multipart' => ['multipart/form-data']],
            security: 'is_granted("'.RoleEnum::ADMIN->value.'")',
            normalizationContext: [
                'groups' => 'role:read',
            ],
            denormalizationContext: [
                'groups' => 'role:files:write',
            ],
        ),
    ],
)]
class Role
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\Column(enumType: GameRoleEnum::class, nullable: true, unique: true)]
    private ?GameRoleEnum $type = null; // Nullable to allow "preview" of a future role

    #[ORM\Column(length: 64)]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $ability;

    #[Vich\UploadableField(mapping: 'role_picture', fileNameProperty: 'pictureName')]
    public ?File $picture = null;

    #[ORM\Column(nullable: true)]
    public ?string $pictureName = null;

    #[ORM\Column(nullable: true)]
    private ?int $minPlayers = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxPerGame = null;

    /** @var GameTeamEnum[] */
    #[ORM\Column(type: Types::SIMPLE_ARRAY, enumType: GameTeamEnum::class)]
    private array $teams = [];

    public function getType(): ?GameRoleEnum
    {
        return $this->type;
    }

    public function setType(?GameRoleEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAbility(): ?string
    {
        return $this->ability;
    }

    public function setAbility(?string $ability): static
    {
        $this->ability = $ability;

        return $this;
    }

    public function getPictureName(): ?string
    {
        return $this->pictureName;
    }

    public function setPictureName(?string $pictureName): static
    {
        $this->pictureName = $pictureName;

        return $this;
    }

    public function getPicture(): ?File
    {
        return $this->picture;
    }

    public function setPicture(?File $picture = null): static
    {
        $this->picture = $picture;

        if ($picture) {
            // Important to update at least one field to trigger the doctrine events
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }

    public function getMinPlayers(): ?int
    {
        return $this->minPlayers;
    }

    public function setMinPlayers(?int $minPlayers): static
    {
        $this->minPlayers = $minPlayers;

        return $this;
    }

    public function getMaxPerGame(): ?int
    {
        return $this->maxPerGame;
    }

    public function setMaxPerGame(?int $maxPerGame): static
    {
        $this->maxPerGame = $maxPerGame;

        return $this;
    }

    /**
     * @return GameTeamEnum[]
     */
    public function getTeams(): array
    {
        return $this->teams;
    }

    public function addTeam(GameTeamEnum $team): static
    {
        $this->teams[] = $team;

        return $this;
    }

    public function removeTeam(GameTeamEnum $team): static
    {
        $this->teams = \array_filter($this->teams, static fn (GameTeamEnum $teamToRemove) => $teamToRemove !== $team);

        return $this;
    }
}
