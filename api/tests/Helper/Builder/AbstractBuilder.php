<?php

namespace App\Tests\Helper\Builder;

/** @template T of object */
abstract class AbstractBuilder
{
    /** @var T|null */
    protected ?object $entity = null;

    /**
     * @return ($returnAll is true ? static[] : static)
     */
    final public function build(?int $count = null, ?bool $returnAll = false): static|array
    {
        $builders = [$this];
        if ($count) {
            for ($i = 0; $i < ($count - 1); ++$i) {
                $clone = clone $this;
                $clone->entity = $clone->doBuild();
                $builders[] = $clone;
            }
        }

        $this->entity = $this->doBuild();

        return $returnAll ? $builders : $this;
    }

    /** @return T */
    final public function getEntity(): object
    {
        if (!$this->entity) {
            throw new \LogicException('Entity has not been built yet');
        }

        return $this->entity;
    }

    /** @return T|null */
    final public function tryGetEntity(): ?object
    {
        return $this->entity;
    }

    /** @return T */
    abstract protected function doBuild(): object;
}
