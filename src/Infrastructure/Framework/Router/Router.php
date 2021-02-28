<?php

declare(strict_types=1);

namespace Sphore\Infrastructure\Framework\Router;

class Router implements RouterInterface
{
	/**
	 * @var Route[][]
	 */
    protected array $routes = [];

	/**
	 * @var DeterminedRoute[][]
	 */
    protected array $determinedRoutes = [];

    public function __construct(
        protected PathResolver $pathResolver,
        protected DeterminedRouteFactory $determinedRouteFactory,
    ) {
    }

    public function getRoute(string $path, string $method): Route | DeterminedRoute | null
    {
        return $this->checkRoutes($path, $method)
            ?? $this->checkDeterminedRoutes($path, $method);
    }

    public function addRoute(Route $route): void
    {
        if (str_contains($route->path, '{')) {
            $this->addDeterminedRoute($route);
        } else {
            $this->addStandardRoute($route);
        }
    }

    public function addRoutes(array $routes): void
    {
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }

    private function addStandardRoute(Route $route): void
    {
        foreach ($route->methods as $method) {
            $this->routes[$method][$route->path] = $route;
        }
    }

    private function addDeterminedRoute(Route $route): void
    {
        $determinedRoute = $this->determinedRouteFactory->createFromRoute($route);
        foreach ($determinedRoute->methods as $method) {
            $this->determinedRoutes[$method][] = $determinedRoute;
        }
    }

    private function checkRoutes(string $path, string $method): Route | null
    {
        return $this->routes[$method][$path] ?? null;
    }

    private function checkDeterminedRoutes(string $path, string $method): DeterminedRoute | null
    {
        $routes = $this->determinedRoutes[$method] ?? null;
        if (!$routes) {
            return null;
        }

        return $this->checkResolvingRoutes($routes, $path);
    }

    private function checkResolvingRoutes(array $routes, string $path): DeterminedRoute | null
    {
        foreach ($routes as $route) {
            $route = $this->resolveRoute($route, $path);
            if ($route) {
                return $route;
            }
        }

        return null;
    }

    private function resolveRoute(DeterminedRoute $route, string $path): DeterminedRoute | null
    {
        $result = $this->pathResolver->resolvePath($route->regex, $path);
        if (!$result->isResolved()) {
            return null;
        }

        $resolvedSlugs = $result->getResolvedSlugs();
        if (count($route->slugs) !== count($resolvedSlugs)) {
            return null;
        }

        $route->slugs = $resolvedSlugs;

        return $route;
    }
}
