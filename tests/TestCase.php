<?php

namespace Outlawplz\Duplicity\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    public function getPackageProviders($app)
    {
        return [
            \Outlawplz\Duplicity\DuplicityServiceProvider::class
        ];
    }
}