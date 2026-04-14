<?php

namespace App\Repository\Game;

use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Entity\User\TempUser;
use App\Entity\User\User;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
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

        $notFinishedGame = $queryBuilder->expr()->andX()
            ->add('g.id IS NOT NULL')
            ->add($queryBuilder->expr()->orX()
                ->add('g.initialisationStep != :initialisationStep')
                ->add('g.runtimeStep IS NULL')
                ->add('g.runtimeStep != :runtimeStep'))
        ;

        $notFinishedManagedGame = $queryBuilder->expr()->andX()
            ->add('mg.id IS NOT NULL')
            ->add($queryBuilder->expr()->orX()
                ->add('mg.initialisationStep != :initialisationStep')
                ->add('mg.runtimeStep IS NULL')
                ->add('mg.runtimeStep != :runtimeStep'))
        ;

        $gameNotFinished = $queryBuilder->expr()->orX()
            ->add($notFinishedGame)
            ->add($notFinishedManagedGame)
        ;

        /** @var Player|null */
        return $queryBuilder
            ->where($userConditions)
            ->andWhere($gameNotFinished)
            ->orderBy('p.createdAt', 'DESC')
            ->setParameter('uuid', $user->getId(), 'uuid')
            ->setParameter('initialisationStep', GameInitialisationStepEnum::FINISH)
            ->setParameter('runtimeStep', GameRuntimeStepEnum::FINISH)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return Player[] */
    public function findWerewolvesByGame(Game $game): array
    {
        /** @var Player[] */
        return $this->createQueryBuilder('p')
            ->innerJoin('p.role', 'r')
            ->where('p.game = :game')
            ->andWhere('r.type = :werewolfType')
            ->setParameter('game', $game->getId(), 'uuid')
            ->setParameter('werewolfType', GameRoleEnum::WEREWOLF)
            ->orderBy('p.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
