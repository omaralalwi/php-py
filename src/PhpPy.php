<?php

declare(strict_types=1);

namespace Omaralalwi\PhpPy;

use Omaralalwi\PhpPy\Managers\ConfigManager;
use RuntimeException;
use InvalidArgumentException;
use UnexpectedValueException;
use Omaralalwi\PhpBuilders\Traits\Buildable;

class PhpPy
{
    use Buildable;

    /**
     * @var ConfigManager
     */
    private ConfigManager $configManager;

    /**
     * @var string
     */
    private string $scriptPath;

    /**
     * @var array
     */
    private array $arguments = [];

    /**
     * @var int
     */
    private int $timeout;

    /**
     * @var array
     */
    private array $env = [];

    /**
     * @var bool
     */
    private bool $parseAsJson = false;

    public function setConfig(ConfigManager $configManager): self
    {
        $this->configManager = $configManager;
        $this->timeout = $configManager->getMaxTimeout();
        return $this;
    }

    /**
     * @param string $path
     * @return self
     * @throws InvalidArgumentException
     */
    public function loadScript(string $path): self
    {
        $allowedDir = $this->configManager->getScriptsDirectory();
        if (empty($allowedDir)) {
            throw new InvalidArgumentException("Script directory is not configured.");
        }

        $allowedDirReal = realpath($allowedDir);
        if (!$allowedDirReal || !is_dir($allowedDirReal)) {
            throw new InvalidArgumentException("Configured script directory is invalid or does not exist.");
        }

        $realPath = realpath($allowedDirReal.'/'.$path);
        if (!$realPath) {
            throw new InvalidArgumentException("Script path does not exist.");
        }

        if (!str_starts_with($realPath, $allowedDirReal . DIRECTORY_SEPARATOR)) {
            throw new InvalidArgumentException("Script path is not within the allowed directory.");
        }

        if (!is_file($realPath)) {
            throw new InvalidArgumentException("Script path is not a file.");
        }

        if (!is_readable($realPath)) {
            throw new InvalidArgumentException("Script file is not readable.");
        }

        if (pathinfo($realPath, PATHINFO_EXTENSION) !== 'py') {
            throw new InvalidArgumentException("Invalid script file extension. Only .py files are allowed.");
        }

        $this->scriptPath = $realPath;
        return $this;
    }

    /**
     * @param array $args
     * @return self
     * @throws InvalidArgumentException
     */
    public function withArguments(array $args): self
    {
        foreach ($args as $arg) {
            if (!is_scalar($arg)) {
                throw new InvalidArgumentException("Arguments must be scalar values.");
            }
        }
        $this->arguments = $args;
        return $this;
    }

    /**
     * @param array $env
     * @return self
     * @throws InvalidArgumentException
     */
    public function withEnvironment(array $env): self
    {
        $blacklistedVars = $this->configManager->getBlacklistedEnvVars();
        foreach ($env as $key => $value) {
            if (in_array(strtoupper($key), $blacklistedVars, true)) {
                throw new InvalidArgumentException("Environment variable '$key' is not allowed.");
            }
        }
        $this->env = $env;
        return $this;
    }

    /**
     * @param int $seconds
     * @return self
     * @throws InvalidArgumentException
     */
    public function timeout(int $seconds): self
    {
        if (!is_numeric($seconds) || $seconds <= 0 || $seconds > $this->configManager->getMaxTimeout()) {
            throw new InvalidArgumentException("Timeout must be a positive integer between 1 and 360 seconds.");
        }
        $this->timeout = $seconds;
        return $this;
    }

    /**
     * @return self
     */
    public function asJson(): self
    {
        $this->parseAsJson = true;
        return $this;
    }

    /**
     * @return mixed
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function run()
    {
        $executor = new CommandExecutor(
            $this->configManager->getPythonExecutable(),
            $this->timeout,
            $this->env,
            $this->parseAsJson,
        );

        return $executor->execute($this->scriptPath, $this->arguments);
    }
}
