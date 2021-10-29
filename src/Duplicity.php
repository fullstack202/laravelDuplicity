<?php

namespace Outlawplz\Duplicity;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

/**
 * @property-read string[] $command
 */
class Duplicity
{
    /** @var string[] */
    protected array $command = [];

    /**
     * @param string|null $cwd
     * @param array|null $env
     * @param mixed|null $input
     * @param float|null $timeout
     */
    public function __construct(
        protected ?string $cwd = null,
        protected ?array  $env = null,
        protected mixed   $input = null,
        protected ?float  $timeout = 3600
    )
    {
    }

    /**
     * @param string $fromDirectory
     * @param string $toUrl
     * @param string|null $force Accepted values are "full" and "incremental".
     * @param callable|null $callback
     * @return string Process output.
     */
    public function backup(string $fromDirectory, string $toUrl, string $force = null, callable $callback = null): string
    {
        $commands = ['duplicity'];

        if (! empty($force)) $commands[] = $force;

        array_push($commands, $fromDirectory, $toUrl);

        array_unshift($this->command, ...$commands);

        $output = $this->runProcess($callback);

        $this->logResults($output);

        return $output;
    }

    /**
     * @param string $fromUrl
     * @param string $toDirectory
     * @param callable|null $callback
     * @return string Process output.
     */
    public function restore(string $fromUrl, string $toDirectory, callable $callback = null): string
    {
        array_unshift($this->command, 'duplicity', 'restore', $fromUrl, $toDirectory);

        return $this->runProcess($callback);
    }

    /**
     * Calculate what would be done, but do not perform any backend actions.
     *
     * @return $this
     */
    public function dryRun(): self
    {
        if (! in_array('--dry-run', $this->command)) $this->command[] = '--dry-run';

        return $this;
    }

    /**
     * Do not use GnuPG to encrypt files on remote system.
     *
     * @return $this
     */
    public function noEncryption(): self
    {
        if (! in_array('--no-encryption', $this->command)) $this->command[] = '--no-encryption';

        return $this;
    }

    /**
     * Duplicity will output the current upload progress and estimated upload time.
     *
     * @return $this
     */
    public function progressBar(): self
    {
        if (! in_array('--progress', $this->command)) $this->command[] = '--progress';

        return $this;
    }

    /**
     * Exclude the file or files matched by shell_pattern.
     *
     * @param string|string[] $excludes
     * @return $this
     */
    public function exclude(string ...$excludes): self
    {
        foreach ($excludes as $path) array_push($this->command, '--exclude', $path);

        return $this;
    }

    /**
     * Instantiate a process and run it.
     *
     * @param callable|null $callback
     * @return string
     */
    protected function runProcess(callable $callback = null): string
    {
        $process = new Process($this->command, $this->cwd, $this->env, $this->input, $this->timeout);

        // Reset command status.
        $this->command = [];

        $process->setIdleTimeout(60);

        $process->mustRun($callback);

        return $process->getOutput();
    }

    /**
     * Log output result in JSON format.
     *
     * @param string $output
     */
    protected function logResults(string $output)
    {
        $properties = [
            'StartTime', 'EndTime', 'ElapsedTime', 'SourceFiles',
            'SourceFileSize', 'NewFiles', 'NewFileSize', 'DeletedFiles',
            'ChangedFiles', 'ChangedFileSize', 'DeltaEntries',
            'RawDeltaSize', 'TotalDestinationSizeChange', 'Errors', 'Plpl'
        ];

        $stats = [];

        foreach ($properties as $property) {
            $found = preg_match("/$property (.*?)([ \n])/", $output, $matches);

            if ($found) $stats[Str::snake($property)] = $matches[1];
        }

        try {
            $log = json_decode(Storage::get('duplicity.json'));
        } catch (FileNotFoundException $error) {
            $log = [];
        }

        array_unshift($log, $stats);

        Storage::put('duplicity.json', json_encode($log, JSON_PRETTY_PRINT));
    }

    /**
     * @param string $property
     * @return string[]|null
     */
    public function __get(string $property): ?array
    {
        if ($property !== 'command') return null;

        return $this->command;
    }
}
