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
}
