<?php

declare(strict_types=1);

namespace Note;

class ParameterBag extends ArrayUtil
{
    // todo: implement filter method

    /**
     * @param  string $name
     *
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * @param  string $name
     * @param  mixed  $value
     *
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        $this->parameters[$name] = $value;
    }
}
