<?php

namespace App\Repository\Game\Period;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Day;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Day>
 */
class DayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Day::class);
    }

    public function findCurrentByGame(Game $game): ?Day
    {
        return $this->findOneBy(['game' => $game, 'resolved' => false]);
    }
}
