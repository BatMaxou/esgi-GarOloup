<?php

namespace App\Entity\Security;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

#[ORM\Entity]
class RefreshToken extends BaseRefreshToken
{
}
