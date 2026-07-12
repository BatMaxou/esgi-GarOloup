<?php

namespace App\Repository\Game\Period;

use App\Entity\Game\Period\Interrupt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Interrupt>
 */
class InterruptRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Interrupt::class);
    }
}
