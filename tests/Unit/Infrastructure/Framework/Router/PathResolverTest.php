<?php

declare(strict_types=1);

namespace Sphore\Tests\Unit\Infrastructure\Framework\Router;

use PHPUnit\Framework\TestCase;
use Sphore\Infrastructure\Framework\Router\PathResolver;
use Sphore\Infrastructure\Framework\Router\PathResolverResult;

class PathResolverTest extends TestCase
{
	private PathResolver $pathResolver;

	protected function setUp(): void
	{
		$this->pathResolver = new PathResolver();
	}

	public function testGetEmptyRegex()
	{
		$regex = $this->pathResolver->getRegex([], "");
		$this->assertEquals("//", $regex);
	}	

	public function testGetRootRegex()
	{
		$regex = $this->pathResolver->getRegex([], "/");
		$this->assertEquals("/\//", $regex);
	}

	public function testGetOneSlugRegex()
	{
		$slugs = ["id"];
		$path = "/profile/{id}";
		$expected = "/\/profile\/(?<id>\w+)/";
		$regex = $this->pathResolver->getRegex($slugs, $path);
		$this->assertEquals($expected, $regex);
	}

	public function testGetTwoSlugsRegex()
	{
		$slugs = ["id", "name"];
		$path = "/profile/{id}/{name}";
		$expected = "/\/profile\/(?<id>\w+)\/(?<name>\w+)/";
		$regex = $this->pathResolver->getRegex($slugs, $path);
		$this->assertEquals($expected, $regex);
	}

	public function testResolveExceptionOnEmptyRegex()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->pathResolver->resolvePath("//", "/");
	}

	public function testResolveSimplePath()
	{
		$path = "/some/test/path";
		$regex = "/\/some\/test\/path/";
		$expected = new PathResolverResult(true, []);
		$result = $this->pathResolver->resolvePath($regex, $path);
		$this->assertEquals($expected, $result);
	}

	public function testResolveOneSlugPath()
	{
		$path = "/user/5";
		$regex = "/\/user\/(?<id>\w+)/";
		$expected = new PathResolverResult(true, ["id" => "5"]);
		$result = $this->pathResolver->resolvePath($regex, $path);
		$this->assertEquals($expected, $result);
	}

	public function testResolveTwoSlugsPath()
	{
		$path = "/user/5/mail/20";
		$regex = "/\/user\/(?<id>\w+)\/mail\/(?<mailId>\w+)/";
		$expected = new PathResolverResult(true, ["id" => "5", "mailId" => 20]);
		$result = $this->pathResolver->resolvePath($regex, $path);
		$this->assertEquals($expected, $result);
	}

	public function testFullResolverSuccess()
	{
		$specPath = "/test/{id}/type/{type}";
		$userPath = "/test/2/type/exception";
		$slugs = ["id", "type"];
		$regex = $this->pathResolver->getRegex($slugs, $specPath);

		$expectedSlugs = ["id" => "2", "type" => "exception"];
		$expected = new PathResolverResult(true, $expectedSlugs);

		$result = $this->pathResolver->resolvePath($regex, $userPath);
		$this->assertEquals($expected, $result);
	}

	public function testResolveFail()
	{
		$path = "/profile/5/";
		$regex = "/\/user\/(?<id>\w+)/";
		$expected = new PathResolverResult(false, []);
		$result = $this->pathResolver->resolvePath($regex, $path);
		$this->assertEquals($expected, $result);
	}
}

