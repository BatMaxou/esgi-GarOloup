<?php

namespace App\Tests\Helper\Behavior;

use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractBehavior
{
    protected HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }
}
