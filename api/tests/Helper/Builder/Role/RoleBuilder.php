<?php

namespace App\Tests\Helper\Builder\Role;

use App\Entity\Role;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;
use App\Fixtures\Factory\RoleFactory;
use App\Tests\Helper\Builder\AbstractBuilder;

/** @extends AbstractBuilder<Role> */
class RoleBuilder extends AbstractBuilder
{
    public ?GameRoleEnum $type = null;
    public ?string $name = null;
    public ?string $description = null;
    public ?string $ability = null;
    /** @var GameTeamEnum[] */
    public ?array $teams = null;
    public ?string $pictureName = null;
    public ?int $minPlayers = null;
    public ?int $maxPerGame = null;

    protected function doBuild(): object
    {
        return RoleFactory::createOne([
            ...($this->name ? ['name' => $this->name] : []),
            ...($this->description ? ['description' => $this->description] : []),
            ...($this->teams ? ['teams' => $this->teams] : []),
            'type' => $this->type,
            'ability' => $this->ability,
            'pictureName' => $this->pictureName,
            'minPlayers' => $this->minPlayers,
            'maxPerGame' => $this->maxPerGame,
        ]);
    }

    public function villager(): static
    {
        $this->type = GameRoleEnum::VILLAGER;

        return $this->withName('Villageois')
            ->withDescription('Description du villageois')
            ->withAbility('Aucune')
            ->withTeam(GameTeamEnum::VILLAGE);
    }

    public function werewolf(): static
    {
        $this->type = GameRoleEnum::WEREWOLF;

        return $this->withName('Loup-garou')
            ->withDescription('Description du Loup-garou')
            ->withAbility('Choisir un joueur à éliminer la nuit')
            ->withTeam(GameTeamEnum::WEREWOLF);
    }

    public function seer(): static
    {
        $this->type = GameRoleEnum::SEER;

        return $this->withName('Voyante')
            ->withDescription('Description de la Voyante')
            ->withAbility('Observer le rôle d\'un joueur chaque nuit')
            ->withTeam(GameTeamEnum::VILLAGE)
            ->withMaxPerGame(1);
    }

    public function witch(): static
    {
        $this->type = GameRoleEnum::WITCH;

        return $this->withName('Sorcière')
            ->withDescription('Description de la Sorcière')
            ->withAbility('Soigner ou empoisonner un joueur la nuit')
            ->withTeam(GameTeamEnum::VILLAGE)
            ->withMaxPerGame(1);
    }

    public function wildChild(): static
    {
        $this->type = GameRoleEnum::WILD_CHILD;

        return $this->withName('Enfant sauvage')
            ->withDescription('Description de l\'Enfant sauvage')
            ->withAbility('Choisir un modèle ; devient Loup-garou si son modèle meurt')
            ->withTeam(GameTeamEnum::VILLAGE)
            ->withMaxPerGame(1);
    }

    public function hunter(): static
    {
        $this->type = GameRoleEnum::HUNTER;

        return $this->withName('Chasseur')
            ->withDescription('Description du Chasseur')
            ->withAbility('Tirer sur un joueur à sa mort')
            ->withTeam(GameTeamEnum::VILLAGE)
            ->withMaxPerGame(1);
    }

    public function withName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function withDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function withAbility(string $ability): static
    {
        $this->ability = $ability;

        return $this;
    }

    public function withTeam(GameTeamEnum $team): static
    {
        $this->teams[] = $team;

        return $this;
    }

    public function withPicture(?string $pictureName = null): static
    {
        $this->pictureName = $pictureName;

        return $this;
    }

    public function withMinPlayers(int $minPlayers): static
    {
        $this->minPlayers = $minPlayers;

        return $this;
    }

    public function withMaxPerGame(int $maxPerGame): static
    {
        $this->maxPerGame = $maxPerGame;

        return $this;
    }
}
