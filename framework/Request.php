<?php

declare(strict_types=1);

namespace Note;

class Request
{
    /** @var string */
    protected $method;

    /** @var ParameterBag */
    protected $query;

    /** @var ParameterBag */
    protected $body;

    /** @var ParameterBag */
    protected $attributes;

    /** @var ParameterBag */
    protected $headers;

    /** @var ArrayUtil */
    protected $files;

    /** @var string */
    protected $scheme;

    /** @var string */
    protected $host;

    /** @var int */
    protected $port;

    /** @var string */
    protected $path;

    /** @var string */
    protected $fragment;

    /** @var string */
    protected $basePath;

    /**
     * @param  string $method
     * @param  string $uri
     * @param  array  $query
     * @param  array  $body
     * @param  array  $attributes
     * @param  array  $headers
     * @param  array  $files
     */
    public function __construct(string $method, string $uri, array $query = [], array $body = [], array $attributes = [], array $headers = [], array $files = [])
    {
    }

    public static function create()
    {
    }
}
