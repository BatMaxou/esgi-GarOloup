<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Helper\When;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class GarOloupApiTestCase extends ApiTestCase
{
    private HttpClientInterface $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        When::setClient($this->client);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $id
     *
     * @return T
     */
    protected function getService(string $id): object
    {
        $service = static::getContainer()->get($id);
        if (!$service instanceof $id) {
            throw new \LogicException(\sprintf('"%s" is not an instance of "%s"', $service::class, $id));
        }

        return $service;
    }
}
