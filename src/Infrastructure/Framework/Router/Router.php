<?php

declare(strict_types=1);

namespace Sphore\Infrastructure\Framework\Router;

class Router implements RouterInterface
{
    protected array $routes = [];

    protected array $determinedRoutes = [];

    protected PathResolver $pathResolver;

    protected Slugger $slugger;

    public function __construct()
    {
        $this->pathResolver = new PathResolver();
        $this->slugger = new Slugger();
    }

    public function getRoute(string $path, string $method): Route|DeterminedRoute|null
    {
		return $this->checkRoutes($path, $method) 
			?? $this->checkDeterminedRoutes($path, $method);
	}

    public function addRoute(Route $route): void
    {
        if (str_contains($route->path, '{')) {
            $slugs = $this->slugger->getSlugsFromPath($route->path);
            $regex = $this->pathResolver->getRegex($slugs, $route->path);
            $determinedRoute = new DeterminedRoute($route, $regex, $slugs);
			foreach($determinedRoute->methods as $method) {
				$this->determinedRoutes[$method][] = $determinedRoute;
			}
        } else {
			foreach($route->methods as $method) {
				$this->routes[$method][$route->path] = $route;
			}
        }
    }

    public function addRoutes(array $routes): void
    {
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }

	private function checkRoutes(string $path, string $method): Route|null
	{
		return $this->routes[$method][$path] ?? null;
	}

	private function checkDeterminedRoutes(string $path, string $method): DeterminedRoute|null
	{
		$routes = $this->determinedRoutes[$method] ?? null;
		if (!$routes) {
			return null;
		}

		return $this->checkResolvedRoutes($routes, $path);

    }

	private function checkResolvedRoutes(array $routes, string $path): DeterminedRoute|null
	{
		foreach ($routes as $route) {
			$result = $this->pathResolver->resolvePath($route->regex, $path);
			if (!$result->isResolved()) {
				continue;
			}

			$resolvedSlugs = $result->getResolvedSlugs();
			if (count($route->slugs) === count($resolvedSlugs)) {
				$route->slugs = $resolvedSlugs;

				return $route;
			}
		}

        return null;
	}
}
