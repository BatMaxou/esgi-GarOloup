<?php

namespace App\Repository\Event\Game;

use App\Entity\Event\Game\GameEvent;
use App\Entity\Game\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameEvent>
 */
class GameEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameEvent::class);
    }

    public function findLastByGame(Game $game): ?GameEvent
    {
        /** @var GameEvent|null */
        return $this->createQueryBuilder('ge')
            ->where('ge.gameId = :gameId')
            ->orderBy('ge.createdAt', 'DESC')
            ->setParameter('gameId', $game->getId()?->toString())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
