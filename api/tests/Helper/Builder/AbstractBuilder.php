<?php

namespace App\Tests\Helper\Builder;

/** @template T of object */
abstract class AbstractBuilder
{
    /** @var T|null */
    protected ?object $entity = null;

    final public function build(?int $count = null): static
    {
        if ($count) {
            for ($i = 0; $i < ($count - 1); ++$i) {
                $clone = clone $this;
                $clone->doBuild();
            }
        }

        $this->entity = $this->doBuild();

        return $this;
    }

    /** @return T */
    final public function getEntity(): object
    {
        if (!$this->entity) {
            throw new \LogicException('Entity has not been built yet');
        }

        return $this->entity;
    }

    /** @return T */
    abstract protected function doBuild(): object;
}
