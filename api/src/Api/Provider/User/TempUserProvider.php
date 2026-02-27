<?php

namespace App\Api\Provider\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Model\TempUser\TempUserTokens;
use App\Entity\User\TempUser;
use App\Repository\TempUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/** @implements ProviderInterface<TempUserTokens> */
final class TempUserProvider implements ProviderInterface
{
    public const TWELVE_HOURS_VALIDITY = 43200;

    public function __construct(
        private readonly Security $security,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly RefreshTokenGeneratorInterface $refreshTokenGenerator,
        private readonly EntityManagerInterface $em,
        private readonly TempUserRepository $tempUserRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): TempUserTokens
    {
        $currentUser = $this->security->getUser();
        if ($currentUser && !$currentUser instanceof TempUser) {
            throw new ConflictHttpException('You already have a user');
        }

        $tempUser = $currentUser ?? $this->getTempUserFromContext($context);

        $output = new TempUserTokens();
        $output->token = $this->jwtManager->create($tempUser);

        $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl($tempUser, self::TWELVE_HOURS_VALIDITY);
        $output->refreshToken = $refreshToken->getRefreshToken();
        $this->em->persist($refreshToken);
        $this->em->flush();

        return $output;
    }

    /** @param mixed[] $context */
    private function getRequestFromContext(array $context): ?Request
    {
        $request = $context['request'] ?? null;
        if (!$request instanceof Request) {
            return null;
        }

        return $request;
    }

    /** @param mixed[] $context */
    private function getTempUserFromContext(array $context): TempUser
    {
        $request = $this->getRequestFromContext($context);
        if (!$request) {
            throw new BadRequestHttpException('Impossible to retrieve request');
        }

        $ip = $request->getClientIp();
        if (!$ip) {
            throw new BadRequestHttpException('Impossible to retrieve ip');
        }

        $username = $request->query->get('username');
        if (!$username) {
            throw new BadRequestHttpException('Undefined username');
        }

        $tempUser = $this->tempUserRepository->findOneBy(['ip' => $ip, 'username' => $username]);
        if (!$tempUser) {
            $tempUser = new TempUser($ip, $username);
            $this->em->persist($tempUser);
        }

        return $tempUser;
    }
}
