<?php

declare(strict_types=1);

namespace Sphore\Infrastructure\Framework\Router;

class PathResolverResult
{
    public function __construct(
        protected bool $success,
        protected array $resolvedSlugs
    ) {
    }

    public function isResolved(): bool
    {
        return $this->success;
    }

    public function getResolvedSlugs(): array
    {
        return $this->resolvedSlugs;
    }
}
