<?php

declare(strict_types=1);

namespace Note;

final class Application extends Container
{
    /** @var string */
    protected $basePath;

    /** @var ArrayUtil */
    protected $environment;

    /** @var Application */
    protected static $app;

    /** @var \Whoops\Run */
    protected $errorHandler;

    /** @var bool */
    protected $runningInConsole;

    /**
     * @param  string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '\/');

        $this->environment = new ArrayUtil($this->parseDotEnv());

        $this->registerFrameworkBindings();

        $this->runningInConsole = \PHP_SAPI === 'cli' || \PHP_SAPI === 'phpdbg';

        $this->errorHandler = new \Whoops\Run();

        if ($this->environment->get('APP_DEBUG', false)) {
            if ($this->runningInConsole) {
                $this->setErrorHandler(new \Whoops\Handler\PlainTextHandler);
            } else {
                $this->setErrorHandler(new \Whoops\Handler\PrettyPageHandler);
            }
        } else {
            // todo: $this->setErrorHandler(new NoteErrorHandler());
            $this->setErrorHandler(new \Whoops\Handler\PlainTextHandler);
        }

        $this->errorHandler->register();

        // todo: set application configuration files
    }

    /**
     * @param  Application $app
     *
     * @return void
     */
    protected static function setInstance(Application $app): void
    {
        static::$app = $app;
    }

    /**
     * @return Application
     */
    public function getInstance(): Application
    {
        return static::$app;
    }

    public function setErrorHandler($handler): void
    {
        $this->errorHandler->pushHandler($handler);
    }

    public function setConfig()
    {
    }

    public function dispatch()
    {
    }

    public function run()
    {
    }

    public function handle()
    {
    }

    public function execute()
    {
    }

    /**
     * @return array
     */
    protected function parseDotEnv(): array
    {
        $file = "{$this->basePath}\\.env";

        if (!file_exists($file)) {
            return [];
        }

        $data = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($data as $row) {
            if (strpos(trim($row), '#') === 0) continue;

            list($name, $value) = explode('=', $row, 2);

            $name = trim($name);
            $value = trim($value, '"\' ');

            if (empty($value)) {
                $value = null;
            } else if ('null' === strtolower($value)) {
                $value = null;
            } else if ('true' === strtolower($value)) {
                $value = true;
            } else if ('false' === strtolower($value)) {
                $value = false;
            } else if (preg_match('/^\d+$/', $value)) {
                $value = intval($value);
            } else if (preg_match('/^-?\d*\.?\d+$/', $value)) {
                $value = floatval($value);
            }

            $env[$name] = $value;
        }

        return $env ?? [];
    }

    /**
     * @return void
     */
    protected function registerFrameworkBindings(): void
    {
        static::setInstance($this);

        foreach ([
            //
            [Application::class, Application::class, false],
            [Container::class,   Container::class,   false],
            [ArrayUtil::class,   ArrayUtil::class,   false],
            //
            ['app', $this, true],
            ['container', $this, true],
        ] as $bind) {
            $this->set($bind[0], $bind[1], $bind[2]);
        }
    }

    final public function __destruct()
    {
        $this->reset();
    }

    private function __clone()
    {
    }
}
