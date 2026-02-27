<?php

namespace App\Tests\Helper\Behavior;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BehaviorResponse
{
    private readonly PropertyAccessorInterface $propertyAccessor;

    public function __construct(
        private readonly ResponseInterface $response,
    ) {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function raw(): ResponseInterface
    {
        return $this->response;
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /** @return mixed[] */
    public function getData(): array
    {
        return $this->response->toArray();
    }

    public function get(string $path): mixed
    {
        return $this->propertyAccessor->getValue($this->getData(), $path);
    }
}
