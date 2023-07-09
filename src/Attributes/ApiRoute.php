<?php

namespace App\Attributes;
use Attribute;
use Symfony\Component\Routing\Annotation\Route;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class ApiRoute extends Route
{
    private string $documentation;
    private array $responses;
    private array $requestScheme;
    private string $fakeName;
    private string $fakePath;
    private string $method;

    public function __construct(array|string $path = null, ?string $name = null, array $requirements = [], array $options = [], array $defaults = [], ?string $host = null, array|string $methods = [], array|string $schemes = [], ?string $condition = null, ?int $priority = null, string $locale = null, string $format = null, bool $utf8 = null, bool $stateless = null, ?string $env = null, string $documentation = '', array $responses = [], array $requestScheme = [], string $fakeName = '', string $fakePath = '')
    {
        parent::__construct($path, $name, $requirements, $options, $defaults, $host, $methods, $schemes, $condition, $priority, $locale, $format, $utf8, $stateless, $env);
        $this->documentation = $documentation;
        $this->responses = $responses;
        $this->requestScheme = $requestScheme;

        $this->fakeName = empty($fakeName) ? $name : $fakeName;
        $this->fakePath = empty($fakePath) ? $path : $fakePath;

        $this->method = empty($methods) ? 'GET' : $methods[0];
    }

    public function getResponses(): array
    {
        return $this->responses;
    }

    public function getDocumentation(): string
    {
        return $this->documentation;
    }

    public function getRequestScheme(): array
    {
        return $this->requestScheme;
    }

    public function getFakeName(): string
    {
        return $this->fakeName;
    }

    public function getFakePath(): string
    {
        return $this->fakePath;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}