<?php

declare(strict_types=1);

namespace Sphore\Infrastructure\Framework\Router;

class DeterminedRoute extends Route
{

    public function __construct(
		Route $route,
		public string $regex,
		public array $slugs,
    ) {
		parent::__construct($route->path, $route->controllerClass, $route->method);
	}
}
