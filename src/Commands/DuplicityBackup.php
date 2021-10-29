<?php

namespace Outlawplz\Duplicity\Commands;

use Illuminate\Console\Command;
use Outlawplz\Duplicity\DatabaseDumperFactory;
use Outlawplz\Duplicity\Duplicity;

class DuplicityBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duplicity:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the application';

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
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function handle(Duplicity $duplicity)
    {
        $dumper = DatabaseDumperFactory::createFromConnection(
            config('database.default')
        );

        $dumpFile = config('duplicity.database_dump');

        $dumper?->dumpToFile($dumpFile);

        $output = $duplicity
            ->noEncryption()
            ->progressBar()
            ->exclude(
                ...config('duplicity.excludes')
            )
            ->backup(
                config('duplicity.backup_directory'),
                config('duplicity.backup_to_url'),
                null,
                function ($type, $buffer) { echo $buffer; }
            );

        $this->line($output);

        if ($dumper) unlink($dumpFile);
    }
}
