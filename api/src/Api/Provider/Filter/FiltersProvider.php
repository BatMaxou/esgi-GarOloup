<?php

namespace App\Api\Provider\Filter;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\Filter\Interface\FilterAwareInterface;

/**
 * @template T of FilterAwareInterface
 *
 * @implements ProviderInterface<T>
 */
abstract class FiltersProvider implements ProviderInterface
{
    /** @return string[] */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?array
    {
        return $this->getClass()::provideFilters();
    }

    /** @return class-string<T> */
    abstract public function getClass(): string;
}
