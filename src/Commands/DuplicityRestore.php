<?php

namespace Outlawplz\Duplicity\Commands;

use Illuminate\Console\Command;
use Outlawplz\Duplicity\Duplicity;

class DuplicityRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duplicity:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore previous backup';

    /**
     * Duplicity backup.
     *
     * @var Duplicity
     */
    protected $duplicity;

    /**
     * Execute the console command.
     *
     * @param Duplicity $duplicity
     *
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function handle(Duplicity $duplicity)
    {
        $duplicity
            ->noEncryption()
            ->restore(
                config('duplicity.restore_url'),
                config('duplicity.restore_to_directory'),
                function ($type, $buffer) { echo $buffer; }
            );
    }
}