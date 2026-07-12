<?php

namespace App\Repository\Game\Period\Action;

use App\Entity\Game\Period\Action\VoteAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VoteAction>
 */
class VoteActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VoteAction::class);
    }
}
