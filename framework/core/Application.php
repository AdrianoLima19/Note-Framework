<?php

declare(strict_types=1);

namespace Note\Core;

use Closure;
use Exception;
use Note\Support\ArrayUtils;

final class Application
{
    /** @var array<string,mixed> */
    protected $binding;

    /** @var array<string,mixed> */
    protected $resolved;

    /** @var Note\Application */
    protected static $app;

    /** @var array<string,mixed> */
    protected $env;

    /**
     * @param  string $basePath
     */
    public function __construct(protected string $basePath)
    {
        $this->env = new ArrayUtils(Env::parseDotEnv($this->basePath));

        self::$app = $this;
    }

    /**
     * @param  string $id
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->binding[$id]);
    }

    /**
     * @param  string $id
     *
     * @return mixed
     */
    public function get(string $id, array $parameters = []): mixed
    {
        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        if (!$this->has($id)) {
            throw new Exception(sprintf('%s::get() Argument #1 $id could not be resolved or [%s] does no exist.', [self::class, $id]), E_ERROR);
        }

        $bind = $this->binding[$id]['entry'];
        $shared = $this->binding[$id]['shared'];

        if ($bind instanceof Closure) {
            $entry = call_user_func($bind, ...$parameters);

            return $shared ? $this->resolved[$id] = $entry : $entry;
        }

        if (!is_string($bind) || (is_string($bind) && !class_exists($bind))) {
            return $shared ? $this->resolved[$id] = $bind : $bind;
        }

        $reflect = new \ReflectionClass($bind);

        if (!$reflect->isInstantiable()) {
            throw new Exception(sprintf('%s::get() Argument #1 $id [%s] is not instantiable', [self::class, $id]), E_ERROR);
        }

        $dependencies = $reflect?->getConstructor()?->getParameters();

        if (empty($dependencies)) {
            $entry = $reflect->newInstanceWithoutConstructor();

            return $shared ? $this->resolved[$id] = $entry : $entry;
        }

        $data = [];
        $typeConverter = [
            'boolean' => 'bool',
            'integer' => 'int',
            'double' => 'float',
        ];

        foreach ($dependencies as $dependency) {
            $name = $dependency->name;
            $position = $dependency->getPosition();

            if (!empty($parameters[$name]) && $data[] = $parameters[$name]) {
                continue;
            }

            if (!empty($parameters[$position]) && (($typeConverter[gettype($parameters[$position])] ?? gettype($parameters[$position])) === $dependency->getType()?->getName())) {
                $data[] = $parameters[$position];
                continue;
            }

            if (class_exists($class = $dependency->getType()?->getName())) {
                $data[] = $this->get($class, $parameters);
                continue;
            }

            if ($dependency->isDefaultValueAvailable()) {
                $data[] = $dependency->getDefaultValue();
                continue;
            }

            if ($dependency->getType()?->allowsNull()) {
                $data[] = null;
                continue;
            }

            throw new Exception(sprintf(
                '%s::get() Argument #2 $parameters expected on parameter [%s], type [%s], but was not found or type was incorrect',
                self::class,
                $name,
                $dependency->getType()?->getName() ?? 'getType()'
            ), E_ERROR);
        }

        $entry = $reflect->newInstanceArgs($data);

        return $shared ? $this->resolved[$id] = $entry : $entry;
    }

    /**
     * @param  string $id
     * @param  mixed  $entry
     * @param  bool   $shared
     *
     * @return void
     */
    public function set(string $id, mixed $entry = null, bool $shared = false): void
    {
        unset(
            $this->binding[$id],
            $this->resolved[$id]
        );

        $this->binding[$id] = compact('entry', 'shared');
    }
}
