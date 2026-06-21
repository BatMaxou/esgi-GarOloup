<?php

namespace App\Repository\Game\Period\Action\NightAction;

use App\Entity\Game\Period\Action\NightAction\InfectAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InfectAction>
 */
class InfectActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfectAction::class);
    }
}
