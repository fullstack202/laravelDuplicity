<?php

namespace Outlawplz\Duplicity\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @property-read string[] $command
 * @method static \Symfony\Component\Process\Process backup(string $fromDirectory, string $toUrl, string $backup = null)
 * @method static \Symfony\Component\Process\Process restore(string $fromUrl, string $toDirectory)
 * @method static \Outlawplz\Duplicity\Duplicity dryRun()
 * @method static \Outlawplz\Duplicity\Duplicity noEncryption()
 * @method static \Outlawplz\Duplicity\Duplicity progressBar()
 * @method static \Outlawplz\Duplicity\Duplicity exclude(array $excludes = [])
 *
 * @see \Outlawplz\Duplicity\Duplicity
 */
class Duplicity extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor(): string
    {
        return \Outlawplz\Duplicity\Duplicity::class;
    }
}
