<?php

declare(strict_types=1);

namespace Note;

class ArrayUtil
{
    /**
     * @param  array<string,mixed> $parameters
     */
    public function __construct(protected array $parameters = [])
    {
    }

    /**
     * @param  string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->parameters[$key]);
    }

    /**
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->parameters[$key] ?? $default;
    }

    /**
     * @param  string $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function set(string $key, mixed $value = null): void
    {
        $this->parameters[$key] = $value;
    }
}
