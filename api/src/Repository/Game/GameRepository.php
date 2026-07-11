<?php

namespace App\Repository\Game;

use App\Entity\Game\Game;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findByJoinCode(string $joinCode): ?Game
    {
        /** @var Game|null */
        return $this->createQueryBuilder('g')
            ->andWhere('g.joinCode = :joinCode')
            ->andWhere('g.initialisationStep = :step')
            ->setParameter('joinCode', $joinCode)
            ->setParameter('step', GameInitialisationStepEnum::NEW)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Game[]
     */
    public function findFinished(): array
    {
        /** @var Game[] */
        return $this->createQueryBuilder('g')
            ->andWhere('g.runtimeStep = :step')
            ->setParameter('step', GameRuntimeStepEnum::FINISH)
            ->getQuery()
            ->getResult()
        ;
    }
}
