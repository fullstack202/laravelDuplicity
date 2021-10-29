<?php

namespace Outlawplz\Duplicity\Tests\Unit;

use Outlawplz\Duplicity\Duplicity;
use Outlawplz\Duplicity\Tests\TestCase;

class DuplicityTest extends TestCase
{
    const TMP = __DIR__ . '/../tmp';

    /** @var Duplicity */
    protected $duplicity;

    public function setUp(): void
    {
        parent::setUp();

        $this->duplicity = new Duplicity();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        system('rm -fr ' . self::TMP);
    }

    /** @test */
    public function duplicity_may_have_dryRun_flag()
    {
        $this->duplicity->dryRun();

        $this->assertContains('--dry-run', $this->duplicity->command);
    }

    /** @test */
    public function duplicity_cannot_have_multiple_dryRun_flags()
    {
        $this->duplicity->dryRun()->dryRun();

        $this->assertCount(1, $this->duplicity->command);
    }

    /** @test */
    public function duplicity_may_have_noEncryption_flag()
    {
        $this->duplicity->noEncryption();

        $this->assertContains('--no-encryption', $this->duplicity->command);
    }

    /** @test */
    public function duplicity_cannot_have_multiple_noEncryption_flags()
    {
        $this->duplicity->noEncryption()->noEncryption();

        $this->assertCount(1, $this->duplicity->command);
    }

    /** @test */
    public function duplicity_may_have_exclude_flag()
    {
        $this->duplicity->exclude('folder');

        $command = implode(' ', $this->duplicity->command);

        $this->assertEquals('--exclude folder', $command);
    }

    /** @test */
    public function duplicity_may_have_multiple_exclude_flag()
    {
        $this->duplicity->exclude('folder1', 'folder2');

        $command = implode(' ', $this->duplicity->command);

        $this->assertEquals('--exclude folder1 --exclude folder2', $command);
    }

    /** @test */
    public function duplicity_may_have_progressBar_flag()
    {
        $this->duplicity->progressBar();

        $this->assertContains('--progress', $this->duplicity->command);
    }

    /** @test */
    public function duplicity_cannot_have_multiple_progressBar_flags()
    {
        $this->duplicity->progressBar()->progressBar();

        $this->assertCount(1, $this->duplicity->command);
    }

    /** @test */
    public function duplicity_can_make_a_backup()
    {
        $this->duplicity
            ->noEncryption()
            ->backup(
            __DIR__,
            'file://' . self::TMP . '/backup'
        );

        $files = glob(self::TMP . '/backup/duplicity-full.*.manifest');

        self::assertCount(1, $files);
    }

    /** @test */
    public function duplicity_can_restore_a_backup()
    {
        $this->duplicity
            ->noEncryption()
            ->backup(
                __DIR__,
                'file://' . self::TMP . '/backup'
            );

        $this->duplicity
            ->noEncryption()
            ->restore(
                'file://' . self::TMP . '/backup',
                self::TMP . '/restore'
            );

        $this->assertFileExists(self::TMP . '/restore/DuplicityTest.php');
    }
}
