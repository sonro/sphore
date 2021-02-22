<?php

namespace Sphore\Infrastructure\Framework\Http;

class Request
{
    public function __construct(
        protected array $post,
        protected string $body,
        protected array $query,
        protected array $headers,
        protected array $cookies,
        protected array $server,
        protected string $path,
        protected string $method,
    ) {
    }

    public static function createFromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_SERVER['REQUEST_URI'];
        $headers = self::createHeadersFromGlobals();

        $body = match ($method) {
            'POST', 'PUT', 'PATCH' => file_get_contents('php://input'),
            default => '',
        };

        return new self(
            post: $_POST,
            body: $body,
            query: $_GET,
            headers: $headers,
            cookies: $_COOKIE,
            server: $_SERVER,
            path: $path,
            method: $method
        );
    }

    protected static function createHeadersFromGlobals(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
			} elseif (\in_array(
				$key, 
				['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'],
				true
			)) {
                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    public function getPost(): array
    {
        return $this->post;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getServer(): array
    {
        return $this->server;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
