<?php

declare(strict_types=1);

namespace Sphore\Tests\Unit\Infrastructure\Framework\Router;

use PHPUnit\Framework\TestCase;
use Sphore\Infrastructure\Framework\Router\PathResolver;
use Sphore\Infrastructure\Framework\Router\PathResolverResult;

class PathResolverTest extends TestCase
{
	public function test_get_empty_regex()
	{
		$pathResolver = $this->createPathResolver();
		$regex = $pathResolver->getRegex([], "");
		$this->assertEquals("/^$/", $regex);
	}	

	public function test_get_root_regex()
	{
		$pathResolver = $this->createPathResolver();
		$regex = $pathResolver->getRegex([], "/");
		$this->assertEquals("/^\/$/", $regex);
	}

	public function test_get_one_slug_regex()
	{
		$pathResolver = $this->createPathResolver();
		$slugs = ["id"];
		$path = "/profile/{id}";
		$expected = "/^\/profile\/(?<id>\w+)$/";
		$regex = $pathResolver->getRegex($slugs, $path);
		$this->assertEquals($expected, $regex);
	}

	public function test_get_two_slugs_regex()
	{
		$pathResolver = $this->createPathResolver();
		$slugs = ["id", "name"];
		$path = "/profile/{id}/{name}";
		$expected = "/^\/profile\/(?<id>\w+)\/(?<name>\w+)$/";
		$regex = $pathResolver->getRegex($slugs, $path);
		$this->assertEquals($expected, $regex);
	}

	public function test_resolve_exception_on_empty_regex()
	{
		$pathResolver = $this->createPathResolver();
		$this->expectException(\InvalidArgumentException::class);
		$pathResolver->resolvePath("/^$/", "/");
	}

	public function test_resolve_simple_path()
	{
		$pathResolver = $this->createPathResolver();
		$path = "/some/test/path";
		$regex = "/^\/some\/test\/path$/";
		$expected = new PathResolverResult(true, []);
		$result = $pathResolver->resolvePath($regex, $path);
		$this->assertEquals($expected, $result);
	}

	public function test_resolve_one_slug_path()
	{
		$pathResolver = $this->createPathResolver();
		$path = "/user/5";
		$regex = "/^\/user\/(?<id>\w+)$/";
		$expected = new PathResolverResult(true, ["id" => "5"]);
		$result = $pathResolver->resolvePath($regex, $path);
		$this->assertEquals($expected, $result);
	}

	public function test_resolve_two_slugs_path()
	{
		$pathResolver = $this->createPathResolver();
		$path = "/user/5/mail/20";
		$regex = "/^\/user\/(?<id>\w+)\/mail\/(?<mailId>\w+)$/";
		$expected = new PathResolverResult(true, ["id" => "5", "mailId" => 20]);
		$result = $pathResolver->resolvePath($regex, $path);
		$this->assertEquals($expected, $result);
	}

	public function test_full_resolver_success()
	{
		$pathResolver = $this->createPathResolver();
		$specPath = "/test/{id}/type/{type}";
		$userPath = "/test/2/type/exception";
		$slugs = ["id", "type"];
		$regex = $pathResolver->getRegex($slugs, $specPath);

		$expectedSlugs = ["id" => "2", "type" => "exception"];
		$expected = new PathResolverResult(true, $expectedSlugs);

		$result = $pathResolver->resolvePath($regex, $userPath);
		$this->assertEquals($expected, $result);
	}

	public function test_resolve_fail()
	{
		$pathResolver = $this->createPathResolver();
		$path = "/profile/5/";
		$regex = "/^\/user\/(?<id>\w+)$/";
		$expected = new PathResolverResult(false, []);
		$result = $pathResolver->resolvePath($regex, $path);
		$this->assertEquals($expected, $result);
	}

	private function createPathResolver(): PathResolver
	{
		return new PathResolver();
	}

}

