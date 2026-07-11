<?php

namespace App\Repository\Game\Period\Action\NightAction;

use App\Entity\Game\Period\Action\NightAction\RevealAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RevealAction>
 */
class RevealActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RevealAction::class);
    }
}
