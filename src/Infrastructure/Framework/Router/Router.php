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

    public function getRoute(string $path): Route|DeterminedRoute|null
    {
        if (!empty($this->routes[$path])) {
            return $this->routes[$path];
        }

        foreach ($this->determinedRoutes as $route) {
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

    public function addRoute(Route $route): void
    {
        if (str_contains($route->path, '{')) {
            $slugs = $this->slugger->getSlugsFromPath($route->path);
            $regex = $this->pathResolver->getRegex($slugs, $route->path);
            $determinedRoute = new DeterminedRoute($route, $regex, $slugs);
            $this->determinedRoutes[] = $determinedRoute;
        } else {
            $this->routes[$route->path] = $route;
        }
    }

    public function addRoutes(array $routes): void
    {
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }
}
