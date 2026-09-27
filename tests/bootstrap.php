<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = 'test';
$_SERVER['KERNEL_CLASS'] = $_ENV['KERNEL_CLASS'] = \App\Kernel::class;
putenv('APP_ENV=test');
putenv('KERNEL_CLASS=App\Kernel');

if ($_SERVER['APP_DEBUG'] ?? false) {
    umask(0000);
}
