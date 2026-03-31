<?php

namespace App\Repository\Game;

use App\Entity\Game\Player;
use App\Entity\User\TempUser;
use App\Entity\User\User;
use App\Enum\Game\GameStepEnum;
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

        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.game', 'g')
            ->leftJoin('p.managedGame', 'mg')
        ;

        $userConditions = $queryBuilder->expr()->orX()
            ->add('p.user = :uuid')
            ->add('p.tempUser = :uuid')
        ;

        $gameConditions = $queryBuilder->expr()->orX()
            ->add('g.id IS NOT NULL')
            ->add('mg.id IS NOT NULL')
        ;

        $stepConditions = $queryBuilder->expr()->orX()
            ->add('g.step != :gameStep')
            ->add('mg.step != :gameStep')
        ;

        /** @var Player|null */
        return $queryBuilder
            ->where($userConditions)
            ->andWhere($gameConditions)
            ->andWhere($stepConditions)
            ->orderBy('p.createdAt', 'DESC')
            ->setParameter('uuid', $user->getId(), 'uuid')
            ->setParameter('gameStep', GameStepEnum::FINISH)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
