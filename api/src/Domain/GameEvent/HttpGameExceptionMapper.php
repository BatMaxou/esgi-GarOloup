<?php

namespace App\Domain\GameEvent;

use App\Domain\GameEvent\Applicator\Exception\AlreadyInAnotherGameException;
use App\Domain\GameEvent\Applicator\Exception\GameException;
use App\Domain\GameEvent\Applicator\Exception\MissingGameException;
use App\Domain\GameEvent\Applicator\Exception\MissingUserException;
use App\Domain\GameEvent\Applicator\Exception\UnauthorizedGameActionException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpGameExceptionMapper
{
    public static function getHttpExceptionFor(GameException $e): HttpException
    {
        $msg = $e->getMessage();

        return match (true) {
            $e instanceof MissingUserException,
            $e instanceof MissingGameException => new BadRequestHttpException($msg),
            $e instanceof UnauthorizedGameActionException => new AccessDeniedHttpException($msg),
            $e instanceof AlreadyInAnotherGameException => new ConflictHttpException($msg),
            default => new BadRequestHttpException($msg),
        };
    }
}
