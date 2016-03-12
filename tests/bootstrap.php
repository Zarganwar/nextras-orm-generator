<?php

require __DIR__ . '/../vendor/autoload.php';

$loader = new Nette\Loaders\RobotLoader;
$loader->addDirectory(__DIR__ . "/../src");
$loader->setCacheStorage(new \Nette\Caching\Storages\FileStorage('temp'));
$loader->register();


