<?php

namespace App\Repository\Game;

use App\Entity\Game\Game;
use App\Enum\Game\GameStepEnum;
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
            ->andWhere('g.step = :step')
            ->setParameter('joinCode', $joinCode)
            ->setParameter('step', GameStepEnum::NEW)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
