<?php

namespace Omaralalwi\PhpPy;

use RuntimeException;
use UnexpectedValueException;

class CommandExecutor
{
    /**
     * @var string
     */
    private $pythonExecutable;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var array
     */
    private $env;

    /**
     * @var bool
     */
    private $parseAsJson;

    /**
     * @var string
     */
    private $stderr;

    /**
     * @param string $pythonExecutable
     * @param int $timeout
     * @param array $env
     * @param bool $parseAsJson
     * @param mixed $expectedResult
     */
    public function __construct(string $pythonExecutable, int $timeout, array $env, bool $parseAsJson)
    {
        $this->pythonExecutable = $pythonExecutable;
        $this->timeout = $timeout;
        $this->env = $env;
        $this->parseAsJson = $parseAsJson;
    }

    /**
     * @param string $scriptPath
     * @param array $arguments
     * @return mixed
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function execute(string $scriptPath, array $arguments)
    {
        $command = array_merge([$this->pythonExecutable, $scriptPath], $arguments);
        $descriptors = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w']  // stderr
        ];

        $process = proc_open(
            $command,
            $descriptors,
            $pipes,
            null,
            $this->env,
            ['bypass_shell' => true]
        );

        if (!is_resource($process)) {
            throw new RuntimeException("Failed to execute Python script.");
        }

        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $output = '';
        $stderr = '';
        $startTime = time();
        $timedOut = false;

        while (true) {
            $read = [$pipes[1], $pipes[2]];
            $remainingTime = $this->timeout - (time() - $startTime);

            if ($remainingTime <= 0) {
                $timedOut = true;
                break;
            }

            $streamsReady = stream_select($read, $write, $except, $remainingTime);

            if ($streamsReady === false) {
                break;
            } elseif ($streamsReady === 0) {
                $timedOut = true;
                break;
            }

            foreach ($read as $pipe) {
                if ($pipe === $pipes[1]) {
                    $output .= fread($pipe, 8192);
                } elseif ($pipe === $pipes[2]) {
                    $stderr .= fread($pipe, 8192);
                }
            }

            $status = proc_get_status($process);
            if (!$status['running']) {
                break;
            }
        }

        foreach ($pipes as $pipe) {
            fclose($pipe);
        }

        if ($timedOut) {
            proc_terminate($process);
            throw new RuntimeException("Python script execution timed out after {$this->timeout} seconds.");
        }

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new RuntimeException("Python script error [$exitCode]: $stderr");
        }

        $this->stderr = $stderr;

        if ($this->parseAsJson && !empty($this->stderr)) {
            throw new RuntimeException("Python script wrote to stderr: $this->stderr");
        }

        if ($this->parseAsJson) {
            $decoded = json_decode($output, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new UnexpectedValueException("Invalid JSON output: " . json_last_error_msg());
            }
            $output = $decoded;
        }

        return $output;
    }
}
