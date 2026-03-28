<?php

namespace App\Tests\Helper\Builder\File;

use App\Tests\Helper\Builder\AbstractBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/** @extends AbstractBuilder<UploadedFile> */
class UploadFileBuilder extends AbstractBuilder
{
    public ?string $path = null;
    public ?string $name = null;

    protected function doBuild(): object
    {
        if (!$this->path || !$this->name) {
            throw new \InvalidArgumentException(\sprintf('Path and name are required to build an %s', UploadedFile::class));
        }

        $pathInfo = \pathinfo($this->path);

        $copyDirectory = \sprintf('%s/copy', $pathInfo['dirname']);
        if (!\is_dir($copyDirectory)) {
            \mkdir($copyDirectory);
        }

        $copyPath = \sprintf('%s/%s', $copyDirectory, $pathInfo['basename']);
        \copy($this->path, $copyPath);

        return new UploadedFile($copyPath, $this->name);
    }

    public function withPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function withName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
