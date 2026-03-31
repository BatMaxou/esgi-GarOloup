<?php

namespace App\Api\Model\Game\Composition;

class CompositionInput
{
    /** @param RoleEntryInput[] $roles */
    public function __construct(
        public readonly array $roles,
    ) {
    }
}
