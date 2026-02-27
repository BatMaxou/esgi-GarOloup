<?php

namespace App\Repository;

use App\Entity\Player;
use App\Entity\User\TempUser;
use App\Entity\User\User;
use App\Enum\GameStepEnum;
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

    /** @return Player[] */
    public function findByUser(UserInterface $user): array
    {
        if (!$user instanceof User && !$user instanceof TempUser) {
            return [];
        }

        /** @var Player[] */
        return $this->createQueryBuilder('p')
            ->where('p.user = :uuid')
            ->orWhere('p.tempUser = :uuid')
            ->setParameter('uuid', $user->getId(), 'uuid')
            ->getQuery()
            ->getResult();
    }

    public function findCurrentByUser(UserInterface $user): ?Player
    {
        if (!$user instanceof User && !$user instanceof TempUser) {
            return null;
        }

        /** @var Player|null */
        return $this->createQueryBuilder('p')
            ->innerJoin('p.game', 'g')
            ->where('p.user = :uuid')
            ->orWhere('p.tempUser = :uuid')
            ->andWhere('g.step != :gameStep')
            ->orderBy('g.createdAt', 'DESC')
            ->setParameter('uuid', $user->getId(), 'uuid')
            ->setParameter('gameStep', GameStepEnum::FINISH)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
