<?php

use App\Kernel;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/../vendor/autoload.php';

new Dotenv()->bootEnv(__DIR__.'/../.env');

$env = $_SERVER['APP_ENV'];
if (!is_string($env)) {
    throw new LogicException('Environment variable APP_ENV is not a string.');
}

$kernel = new Kernel($env, (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$doctrine = $kernel->getContainer()->get('doctrine');
if (!$doctrine instanceof Registry) {
    throw new LogicException('Doctrine is not available.');
}

return $doctrine->getManager();
