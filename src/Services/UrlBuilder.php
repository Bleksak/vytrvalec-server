<?php

declare(strict_types=1);

namespace App\Services;

final class UrlBuilder
{
    private string $base = '';

    /** @var list<string> */
    private array $segments = [];

    /** @var array<string, int|string|list<string|int>> */
    private array $queryParams = [];

    public function __construct(string $base = '')
    {
        $this->base = \rtrim($base, '/');
    }

    public function path(string $segment): self
    {
        $this->segments[] = \trim($segment, '/');
        return $this;
    }

    public function argument(string|int $arg): self
    {
        $this->segments[] = \trim((string) $arg, '/');
        return $this;
    }

    /** @param string|int|list<string|int> $value */
    public function query(string $key, string|int|array $value): self
    {
        if (\is_array($value)) {
            foreach ($value as $v) {
                \assert(isset($this->queryParams[$key]));
                \assert(\is_array($this->queryParams[$key]));

                $this->queryParams[$key][] = $v;
            }
        } else {
            $this->queryParams[$key] = $value;
        }
        return $this;
    }

    // Build final URL
    public function build(): string
    {
        $path = \implode('/', $this->segments);
        $url = $this->base . '/' . $path;

        if ($this->queryParams !== []) {
            $url .= '?' . \http_build_query($this->queryParams);
        }

        return $url;
    }
}
