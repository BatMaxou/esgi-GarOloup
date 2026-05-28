<?php

namespace App\Repository\Game\Period\Action;

use App\Entity\Game\Period\Action\DayAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DayAction>
 */
class DayActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DayAction::class);
    }
}
