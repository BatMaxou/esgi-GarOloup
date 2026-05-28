<?php

namespace App\Repository\Game\Period\Action;

use App\Entity\Game\Period\Action\NightAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NightAction>
 */
class NightActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NightAction::class);
    }
}
