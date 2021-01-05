<?php

declare(strict_types=1);

namespace Sphore\Infrastructure\Framework\Router;

class Slugger
{
    public function getSlugsFromPath(string $path): array
    {
        $preg_result = preg_match_all('/{([a-zA-Z]+)}/', $path, $matches, PREG_SET_ORDER);
        if (false === $preg_result) {
            throw new \Exception('Unable to use regex in slugger');
        }
        if ($preg_result) {
			foreach ($matches as $match) {
				$slugs[] = $match[1];
			}
            return $slugs;
        }

        return [];
    }

}
