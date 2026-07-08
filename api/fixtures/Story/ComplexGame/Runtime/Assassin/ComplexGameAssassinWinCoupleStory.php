<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class ComplexGameAssassinWinCoupleStory extends ComplexGameAssassinWinVote4Story
{
    public function execute(): void
    {
        parent::execute();

        $assassinPlayerBuilder = $this->getState(self::ASSASSIN);
        \assert($assassinPlayerBuilder instanceof PlayerBuilder);
        $villager1PlayerBuilder = $this->getState(self::VILLAGER_1);
        \assert($villager1PlayerBuilder instanceof PlayerBuilder);

        $assassin = $assassinPlayerBuilder->getEntity();
        $villager1 = $villager1PlayerBuilder->getEntity();
        $assassinId = $assassin->getId()?->toString();
        \assert(null !== $assassinId);
        $villager1Id = $villager1->getId()?->toString();
        \assert(null !== $villager1Id);

        $assassinOriginalRole = $assassin->getRole();
        \assert(null !== $assassinOriginalRole);
        $villager1OriginalRole = $villager1->getRole();
        \assert(null !== $villager1OriginalRole);

        $assassinTeam = $assassin->getTeam();
        $assassin->setRole(
            ThereIs::aLoverRole()
                ->wrapping($assassinOriginalRole)
                ->withPartner($villager1Id)
                ->build()
                ->getEntity(),
        );
        $assassin->setTeam($assassinTeam);

        $villager1Team = $villager1->getTeam();
        $villager1->setRole(
            ThereIs::aLoverRole()
                ->wrapping($villager1OriginalRole)
                ->withPartner($assassinId)
                ->build()
                ->getEntity(),
        );
        $villager1->setTeam($villager1Team);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-assassin-win-couple-';
    }
}
