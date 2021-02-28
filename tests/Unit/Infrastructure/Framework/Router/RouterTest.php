<?php

declare(strict_types=1);

namespace Sphore\Tests\Unit\Infrastructure\Framework\Router;

use PHPUnit\Framework\TestCase;
use Sphore\Infrastructure\Framework\Router\DeterminedRoute;
use Sphore\Infrastructure\Framework\Router\DeterminedRouteFactory;
use Sphore\Infrastructure\Framework\Router\PathResolver;
use Sphore\Infrastructure\Framework\Router\Route;
use Sphore\Infrastructure\Framework\Router\Router;
use Sphore\Infrastructure\Framework\Router\Slugger;

class RouterTest extends TestCase
{
	public function test_empty_router_gets_null()
	{
		$router = $this->createRouter();
		$this->assertNull($router->getRoute("/", "GET"));
	}

	public function test_path_mismatch_gets_null()
	{
		$router = $this->routerWithOneRoute("/test/route");
		$this->assertNull($router->getRoute("/", "GET"));
	}

	public function test_path_match_gets_route()
	{
		$path = "/test/route";
		$router = $this->routerWithOneRoute($path);
		$result = $router->getRoute($path, "GET");
		$this->assertNotNull($result);
		$this->assertEquals($path, $result->path);
	}

	public function test_determined_route_match()
	{
		$router = $this->routerWithOneRoute("/test/{slug}");
		$result = $router->getRoute("/test/50", "GET");
		$this->assertNotNull($result);
		$this->assertInstanceOf(DeterminedRoute::class, $result);
	}

	public function test_equal_path_different_method()
	{
		$path = "/test";
		$controllerClass = "ControllerClass";
		$router = $this->createRouter();
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
		$router = $this->createRouter();
		$this->addOneRoute($router, $path);

		return $router;
	}

	private function addOneRoute($router, $path): void
	{
		$route = new Route($path, "ControllerClass", "methodName");
		$router->addRoute($route);
	}

	private function createRouter(): Router
	{
		$pathResolver = new PathResolver();
		$slugger = new Slugger();
		$determinedRouteFactory = new DeterminedRouteFactory(
			$pathResolver, 
			$slugger
		);

		return new Router($pathResolver, $determinedRouteFactory);
	}
}
