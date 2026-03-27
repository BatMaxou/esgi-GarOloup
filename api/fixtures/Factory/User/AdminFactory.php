<?php

namespace App\Fixtures\Factory\User;

use App\Entity\User\Admin;

/** @extends AbstractUserFactory<Admin> */
class AdminFactory extends AbstractUserFactory
{
    public static function class(): string
    {
        return Admin::class;
    }
}
