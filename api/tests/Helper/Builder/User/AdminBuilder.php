<?php

namespace App\Tests\Helper\Builder\User;

use App\Fixtures\Factory\User\AdminFactory;

class AdminBuilder extends UserBuilder
{
    protected function doBuild(): object
    {
        return AdminFactory::createOne([
            ...($this->username ? ['username' => $this->username] : []),
            ...($this->password ? ['password' => $this->password] : []),
            ...($this->email ? ['email' => $this->email] : []),
            ...($this->resetToken ? ['resetToken' => $this->resetToken] : []),
        ]);
    }
}
