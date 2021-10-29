<?php

namespace Outlawplz\Duplicity;

use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Databases\PostgreSql;
use Spatie\DbDumper\Databases\Sqlite;
use Spatie\DbDumper\DbDumper;

class DatabaseDumperFactory
{
    const DRIVERS = [
        'mysql' => MySql::class,
        'sqlite' => Sqlite::class,
        'pgsql' => PostgreSql::class,
    ];

    /**
     * @param string $connection
     * @return DbDumper|null
     */
    public static function createFromConnection(string $connection): ?DbDumper
    {
        if (! $connection) return null;

        /** @var DbDumper $dumper */
        $dumper = self::DRIVERS[$connection];

        $database = config("database.connections.$connection");

        $dumper = $dumper::create()
            ->setDbName($database['database'])
            ->setUserName($database['username'] ?? '')
            ->setPassword($database['password'] ?? '');

        return $dumper;
    }
}
