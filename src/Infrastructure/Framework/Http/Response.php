<?php

declare(strict_types=1);

namespace Sphore\Infrastructure\Framework\Http;

class Response
{
    /**
     * @param string   $content
     * @param int      $httpStatus
     * @param string[] $headers
     */
    public function __construct(
        protected string $content = '',
        protected int $status = HttpStatus::OK,
        protected array $headers = [],
    ) {
    }

	public function send(): void
	{
		foreach ($this->headers as $key => $value) {
			header("$key: $value");
		}

		http_response_code($this->status);
		echo $this->content;
	}
}
