<?php

namespace App\Domain\Interceptor;

use App\Domain\Interceptor\Interface\InterceptorInterface;
use App\Entity\Game\Game;

class InterceptorHandler
{
    /** @var InterceptorInterface[] */
    private array $interceptors;

    /** @param iterable<InterceptorInterface> $interceptors */
    public function __construct(iterable $interceptors)
    {
        $this->interceptors = \iterator_to_array($interceptors);
    }

    public function hasPendingAction(Game $game): bool
    {
        foreach ($this->interceptors as $interceptor) {
            if ($interceptor->hasPendingAction($game)) {
                return true;
            }
        }

        return false;
    }
}
