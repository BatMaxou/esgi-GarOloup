<?php

namespace App\Repository\Game;

use App\Entity\Game\Game;
use App\Entity\Game\Night;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Night>
 */
class NightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Night::class);
    }

    public function findCurrentByGame(Game $game): ?Night
    {
        return $this->findOneBy(['game' => $game, 'resolved' => false]);
    }
}
