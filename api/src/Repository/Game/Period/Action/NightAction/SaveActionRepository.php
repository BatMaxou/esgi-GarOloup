<?php

namespace App\Repository\Game\Period\Action\NightAction;

use App\Entity\Game\Period\Action\NightAction\SaveAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SaveAction>
 */
class SaveActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SaveAction::class);
    }
}
