<?php

declare(strict_types=1);

namespace Omaralalwi\PhpPy\Managers;

use InvalidArgumentException;

class ConfigManager
{
    /**
     * @var string
     */
    private string $scriptsDirectory;

    /**
     * @var string
     */
    private string $pythonExecutable;

    private int $maxTimeout;

    /**
     * @var array
     */
    private array $blacklistedEnvVars = [
        'PATH',
        'PYTHONPATH',
        'LD_LIBRARY_PATH',
        'LD_PRELOAD',
        'PYTHONHOME'
    ];

    /**
     * @param array $config
     * @throws InvalidArgumentException
     */
    public function __construct(array $config = [])
    {
        if (empty($config['scripts_directory'])) {
            throw new InvalidArgumentException("Scripts directory is not configured.");
        }

        if (!is_dir($config['scripts_directory'])) {
            throw new InvalidArgumentException("Scripts directory is invalid or does not exist.");
        }

        if (empty($config['python_executable'])) {
            throw new InvalidArgumentException("Python executable path is not configured.");
        }

        if (!is_executable($config['python_executable'])) {
            throw new InvalidArgumentException("Python executable path is not executable.");
        }

        $this->scriptsDirectory = $config['scripts_directory'];
        $this->pythonExecutable = $config['python_executable'];
        $this->maxTimeout = $config['max_timeout'];
    }

    /**
     * @return string
     */
    public function getScriptsDirectory(): string
    {
        return $this->scriptsDirectory;
    }

    /**
     * @return string
     */
    public function getPythonExecutable(): string
    {
        return $this->pythonExecutable;
    }

    /**
     * @return array
     */
    public function getBlacklistedEnvVars(): array
    {
        return $this->blacklistedEnvVars;
    }

    public function getMaxTimeout(): int
    {
        return $this->maxTimeout;
    }

    /**
     * @param string $pythonExecutable
     * @return self
     * @throws InvalidArgumentException
     */
    public function withPythonExecutable(string $pythonExecutable): self
    {
        $realPath = realpath($pythonExecutable);
        if (!$realPath) {
            throw new InvalidArgumentException("Invalid Python executable path.");
        }

        if (!is_executable($realPath)) {
            throw new InvalidArgumentException("Python executable is not executable.");
        }

        $this->pythonExecutable = $realPath;
        return $this;
    }
}
