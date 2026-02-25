<?php

namespace App\Controller;

use App\Entity\User\TempUser;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly EntityManagerInterface $em,
    ) {
    }

    #[Route('/api/test', name: 'app_test')]
    public function test(Request $request): Response
    {
        return new JsonResponse(['message' => 'Hello World!'], Response::HTTP_OK);
    }

    #[Route('/api/user/test', name: 'app_user_test')]
    public function user(): Response
    {
        return new JsonResponse(['message' => 'Hello World!'], Response::HTTP_OK);
    }

    #[Route('/api/game/test', name: 'app_game_test')]
    public function game(): Response
    {
        return new JsonResponse(['message' => 'Hello World!'], Response::HTTP_OK);
    }

    #[Route('/api/generate/token', name: 'app_generate_token')]
    public function generateToken(): Response
    {
        $tempUser = new TempUser();
        $this->em->persist($tempUser);
        $this->em->flush();

        return new JsonResponse(['token' => $this->jwtManager->create($tempUser)], Response::HTTP_OK);
    }
}
