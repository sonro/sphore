<?php

declare(strict_types=1);

namespace Sphore\Infrastructure\Framework\Router;

class DeterminedRouteFactory
{
    public function __construct(
        protected PathResolver $pathResolver,
        protected Slugger $slugger,
    ) {
    }

	public function createFromRoute(Route $route): DeterminedRoute
	{
		$slugs = $this->slugger->getSlugsFromPath($route->path);
		$regex = $this->pathResolver->getRegex($slugs, $route->path);

		return new DeterminedRoute($route, $regex, $slugs);
	}
}
