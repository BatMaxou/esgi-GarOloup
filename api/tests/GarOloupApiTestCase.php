<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Helper\When;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class GarOloupApiTestCase extends ApiTestCase
{
    private HttpClientInterface $client;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem();
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

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->getService(EntityManagerInterface::class);
    }

    protected function getMockedAssetPath(string $assetPath): string
    {
        return \sprintf('%s/../fixtures/assets/%s', __DIR__, $assetPath);
    }

    protected function tearDown(): void
    {
        $path = static::getContainer()->getParameter('app.ssr_public_uploads_path');
        if (!\is_string($path)) {
            return;
        }

        $path = \sprintf('.%s', $path);

        if ($this->filesystem->exists($path)) {
            $this->filesystem->remove($path);
        }

        parent::tearDown();
    }
}
