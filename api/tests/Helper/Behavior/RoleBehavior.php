<?php

namespace App\Tests\Helper\Behavior;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class RoleBehavior extends AbstractBehavior
{
    public function list(): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('GET', '/api/roles'));
    }

    public function get(?string $roleId): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('GET', \sprintf('/api/roles/%s', $roleId)));
    }

    /** @param array<string, mixed> $data */
    public function update(?string $roleId, ?array $data = []): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('PATCH', \sprintf('/api/roles/%s', $roleId), [
            'headers' => $this->getPatchHeaders(),
            'json' => $data,
        ]));
    }

    /** @param array<string, UploadedFile> $files */
    public function updateFiles(?string $roleId, ?array $files = []): BehaviorResponse
    {
        return new BehaviorResponse($this->client->request('POST', \sprintf('/api/roles/%s/files', $roleId), [
            'headers' => $this->getMultipartHeaders(),
            'extra' => [
                'files' => $files,
            ],
        ]));
    }
}
