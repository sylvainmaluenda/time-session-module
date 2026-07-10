<?php

declare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/core/ServiceContainer.php';
require_once __DIR__ . '/core/ContainerFactory.php';
require_once __DIR__ . '/infrastructure/ViteAssetManager.php';
require_once __DIR__ . '/infrastructure/ReviewMailer.php';
