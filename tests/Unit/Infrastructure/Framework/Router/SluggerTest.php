<?php

declare(strict_types=1);

namespace Sphore\Test\Unit\Infrastructure\Framework\Router;

use PHPUnit\Framework\TestCase;
use Sphore\Infrastructure\Framework\Router\Slugger;

class SluggerTest extends TestCase
{
    private Slugger $slugger;

    protected function setUp(): void
    {
        $this->slugger = new Slugger();
    }

    public function testEmptyPath()
    {
        $slugs = $this->slugger->getSlugsFromPath('');
        $this->assertEmpty($slugs);
    }

    public function testNoSlugs()
    {
        $slugs = $this->slugger->getSlugsFromPath('/test/path');
        $this->assertEmpty($slugs);
    }

    public function testOneSlug()
    {
        $slugs = $this->slugger->getSlugsFromPath('/test/{slug}');
        $this->assertNotEmpty($slugs);
        $this->assertEquals(['slug'], $slugs);
    }

    public function testTwoSlugs()
    {
        $slugs = $this->slugger->getSlugsFromPath('/test/{slug}/another/{id}');
        $this->assertNotEmpty($slugs);
        $this->assertEquals(['slug', 'id'], $slugs);
    }
}
