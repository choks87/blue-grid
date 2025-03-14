<?php

use BlueGrid\Tests\Integration\WebServerTestHook;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

(new Filesystem())->remove(
    [
        __DIR__.'/../var/cache',
        __DIR__.'/../var/log',
    ]
);

$hook = new WebServerTestHook('localhost', 9501, 10, '/tmp/test-web-server.pid');
$hook->beforeTest();

register_shutdown_function(static fn() => $hook->afterTest());