<?php

namespace App\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Model\TempUser\TempUserTokens;
use App\Entity\TempUser;
use App\Repository\TempUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * @implements ProviderInterface<TempUserTokens|null>
 */
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

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?TempUserTokens
    {
        $currentUser = $this->security->getUser();
        if ($currentUser) {
            throw new ConflictHttpException('You already have a user');
        }

        $ip = $this->getIpFromContext($context);
        if (!$ip) {
            return null;
        }

        $currentUser = $this->tempUserRepository->findOneBy(['ip' => $ip]);
        if (!$currentUser) {
            $currentUser = new TempUser($ip);
            $this->em->persist($currentUser);
        }

        $output = new TempUserTokens();
        $output->token = $this->jwtManager->create($currentUser);

        $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl($currentUser, self::TWELVE_HOURS_VALIDITY);
        $output->refreshToken = $refreshToken->getRefreshToken();
        $this->em->persist($refreshToken);
        $this->em->flush();

        return $output;
    }

    private function getIpFromContext(array $context): ?string
    {
        $request = $context['request'] ?? null;
        if (!$request instanceof Request) {
            return null;
        }

        return $request->getClientIp();
    }
}
