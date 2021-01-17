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
		$this->assertNull($router->getRoute("/", "GET"));
	}

	public function testEmptyRouterAddOneRouteNotMatching()
	{
		$router = $this->routerWithOneRoute("/test/route");
		$this->assertNull($router->getRoute("/", "GET"));
	}

	public function testEmptyRouterAddOneRouteMatching()
	{
		$path = "/test/route";
		$router = $this->routerWithOneRoute($path);
		$result = $router->getRoute($path, "GET");
		$this->assertNotNull($result);
		$this->assertEquals($path, $result->path);
	}

	public function testAddDeterminedRoute()
	{
		$router = $this->routerWithOneRoute("/test/{slug}");
		$result = $router->getRoute("/test/50", "GET");
		$this->assertNotNull($result);
		$this->assertEquals($result->slugs["slug"], "50");
	}

	public function testResolveRouteByMethod()
	{
		$path = "/test";
		$controllerClass = "ControllerClass";
		$router = new Router();
		$router->addRoutes([
			new Route($path, $controllerClass, "getMethod", ["GET"]),
			new Route($path, $controllerClass, "postMethod", ["POST"]),
		]);

		$getRouteResult = $router->getRoute($path, "GET");
		$postRouteResult = $router->getRoute($path, "POST");
		$this->assertNotNull($getRouteResult);
		$this->assertNotNull($postRouteResult);
		$this->assertNotEquals($getRouteResult, $postRouteResult, "Post route should have different action");
		
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
