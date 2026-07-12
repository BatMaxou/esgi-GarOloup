<?php

namespace App\Repository\Game\Period\Action;

use App\Entity\Game\Period\Action\InterruptAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InterruptAction>
 */
class InterruptActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterruptAction::class);
    }
}
