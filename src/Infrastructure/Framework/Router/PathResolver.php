<?php

declare(strict_types=1);

namespace Sphore\Infrastructure\Framework\Router;

class PathResolver
{
    public function getRegex(array $slugs, string $path): string
    {
		// escape / in regex string
        $regex = str_replace('/', '\/', $path);

        foreach ($slugs as $slug) {
            // generate regex named subgroup in place of each slug
            $regex = str_replace('{'.$slug.'}', "(?<$slug>\w+)", $regex);
        }

		// add beginning and end regex match to stop longer paths being matched
        return "/^$regex$/";
    }

    public function resolvePath(string $pathRegex, string $path): PathResolverResult
    {
		if ($pathRegex === "//") {
            throw new \InvalidArgumentException('Regex must not be empty');
		}

        $pregResult = preg_match($pathRegex, $path, $matches);

        if (false === $pregResult) {
            throw new \Exception('Unable to use regex in slugger');
        }

        if (0 === $pregResult) {
            return new PathResolverResult(false, []);
        }

		$slugs = $this->getSlugsFromPregMatches($matches);

		return new PathResolverResult(true, $slugs);
    }

	protected function getSlugsFromPregMatches(array $matches): array
	{
        $slugs = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $slugs[$key] = $value;
            }
        }

		return $slugs;
	}
}
