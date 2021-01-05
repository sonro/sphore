<?php

declare(strict_types=1);

namespace Sphore\Test\Unit\Infrastructure\Framework\Router;

use PHPUnit\Framework\TestCase;
use Sphore\Infrastructure\Framework\Router\Route;
use Sphore\Infrastructure\Framework\Router\Router;

class RouterTest extends TestCase
{
	public function testEmptyRouterGetsNull()
	{
		$router = new Router();
		$this->assertNull($router->getRoute("/"));
	}

	public function testEmptyRouterAddOneRouteNotMatching()
	{
		$router = $this->routerWithOneRoute("/test/route");
		$this->assertNull($router->getRoute("/"));
	}

	public function testEmptyRouterAddOneRouteMatching()
	{
		$path = "/test/route";
		$router = $this->routerWithOneRoute($path);
		$result = $router->getRoute($path);
		$this->assertNotNull($result);
		$this->assertEquals($path, $result->path);
	}

	public function testAddDeterminedRoute()
	{
		$router = $this->routerWithOneRoute("/test/{slug}");
		$result = $router->getRoute("/test/50");
		$this->assertNotNull($result);
		$this->assertEquals($result->slugs["slug"], "50");
	}

	private function routerWithOneRoute($path): Router
	{
		$router = new Router();
		$this->addOneRoute($router, $path);

		return $router;
	}

	private function addOneRoute($router, $path)
	{
		$route = new Route($path, "ControllerClass", "methodName");
		$router->addRoute($route);
	}

}
