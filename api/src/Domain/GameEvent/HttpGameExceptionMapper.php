<?php

namespace App\Domain\GameEvent;

use App\Domain\GameEvent\Exception\AlreadyInAnotherGameException;
use App\Domain\GameEvent\Exception\GameException;
use App\Domain\GameEvent\Exception\MissingGameException;
use App\Domain\GameEvent\Exception\MissingUserException;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Exception\UsernameAlreadyTakenException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class HttpGameExceptionMapper
{
    public static function getHttpExceptionFor(GameException $e): HttpException
    {
        $msg = $e->getMessage();

        return match (true) {
            $e instanceof MissingUserException,
            $e instanceof MissingGameException => new BadRequestHttpException($msg),
            $e instanceof UnauthorizedGameActionException => new AccessDeniedHttpException($msg),
            $e instanceof PlayerNotFoundException => new NotFoundHttpException($msg),
            $e instanceof AlreadyInAnotherGameException,
            $e instanceof UsernameAlreadyTakenException => new ConflictHttpException($msg),
            default => new BadRequestHttpException($msg),
        };
    }
}
