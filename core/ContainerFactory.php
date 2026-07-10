<?php

namespace Pscsession\Core;

use Pscsession\infrastructure\ReviewMailer;
use Pscsession\infrastructure\ViteAssetManager;

final class ContainerFactory
{
    public static function build(string $modulePath, string $moduleUri, string $devServer): ServiceContainer
    {
        $container = new ServiceContainer();

        $container->set(ViteAssetManager::class, fn() => new ViteAssetManager($modulePath, $moduleUri, $devServer));

        $container->set(ReviewMailer::class, fn() => new ReviewMailer());

        return $container;
    }
}
