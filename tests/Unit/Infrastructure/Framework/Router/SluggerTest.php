<?php

declare(strict_types=1);

namespace Sphore\Test\Unit\Infrastructure\Framework\Router;

use PHPUnit\Framework\TestCase;
use Sphore\Infrastructure\Framework\Router\Slugger;

class SluggerTest extends TestCase
{
    public function test_empty_path()
    {
		$slugger = $this->createSlugger();
        $slugs = $slugger->getSlugsFromPath('');
        $this->assertEmpty($slugs);
    }

    public function test_no_slugs()
    {
		$slugger = $this->createSlugger();
        $slugs = $slugger->getSlugsFromPath('/test/path');
        $this->assertEmpty($slugs);
    }

    public function test_one_slugs()
    {
		$slugger = $this->createSlugger();
        $slugs = $slugger->getSlugsFromPath('/test/{slug}');
        $this->assertNotEmpty($slugs);
        $this->assertEquals(['slug'], $slugs);
    }

    public function test_two_slugs()
    {
		$slugger = $this->createSlugger();
        $slugs = $slugger->getSlugsFromPath('/test/{slug}/another/{id}');
        $this->assertNotEmpty($slugs);
        $this->assertEquals(['slug', 'id'], $slugs);
    }

	private function createSlugger(): Slugger
	{
		return new Slugger();
	}
}
