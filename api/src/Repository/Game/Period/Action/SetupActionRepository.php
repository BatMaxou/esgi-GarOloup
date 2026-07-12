<?php

namespace App\Repository\Game\Period\Action;

use App\Entity\Game\Period\Action\SetupAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SetupAction>
 */
class SetupActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SetupAction::class);
    }
}
