<?php

declare(strict_types=1);

namespace Sphore\Infrastructure\Framework\Router;

interface RouterInterface
{
    public function getRoute(string $path): ?Route;

    public function addRoute(Route $route): void;

    public function addRoutes(array $route): void;
}
