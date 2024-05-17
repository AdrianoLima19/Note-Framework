<?php

declare(strict_types=1);

namespace Note;

class Response
{
    /** @var  string */
    protected $content;

    /** @var  int */
    protected $statusCode;

    /** @var  array */
    protected $headers;

    /**
     * @param  string $content
     * @param  int    $statusCode
     * @param  array  $headers
     */
    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
    }
}
