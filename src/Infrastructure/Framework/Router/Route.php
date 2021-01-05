<?php

declare(strict_types=1);

namespace Sphore\Infrastructure\Framework\Router;

class Route
{
    public function __construct(
        public string $path,
        public string $controllerClass,
        public string $method,
    ) {
	}
}
