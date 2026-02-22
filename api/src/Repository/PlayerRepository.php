<?php

namespace App\Repository;

use App\Entity\Player;
use App\Entity\TempUser;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Player>
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    public function findByUser(UserInterface $user): array
    {
        if (!$user instanceof User && !$user instanceof TempUser) {
            return [];
        }

        return $this->createQueryBuilder('p')
            ->where('p.user = :uuid')
            ->orWhere('p.tempUser = :uuid')
            ->setParameter('uuid', $user->getId(), 'uuid')
            ->getQuery()
            ->getResult();
    }
}
