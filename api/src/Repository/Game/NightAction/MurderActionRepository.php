<?php

namespace App\Repository\Game\NightAction;

use App\Entity\Game\NightAction\MurderAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MurderAction>
 */
class MurderActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MurderAction::class);
    }
}
