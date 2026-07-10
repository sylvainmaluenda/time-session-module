<?php

declare(strict_types=1);

namespace Pscsession\core;

use Closure;
use RuntimeException;

final class ServiceContainer
{
    private array $factories = [];
    private array $instances = [];

    public function set(string $id, Closure $factory): void
    {
        $this->factories[$id] = $factory;
    }

    public function get(string $id): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->factories[$id])) {
            throw new RuntimeException(sprintf('Service "%s" is not registered.', $id));
        }

        $service = $this->factories[$id]($this);
        $this->instances[$id] = $service;

        return $service;
    }

    public function has(string $id): bool
    {
        return isset($this->factories[$id]);
    }
}
