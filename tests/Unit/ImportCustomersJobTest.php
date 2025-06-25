<?php

namespace Tests\Unit;

use App\Contracts\CustomerImporterInterface;
use App\Jobs\ImportCustomersJob;
use Mockery;
use Tests\TestCase;

class ImportCustomersJobTest extends TestCase
{
    /**
     * Test job calls importer with correct count.
     */
    public function test_job_calls_importer_with_correct_count(): void
    {
        $mockImporter = Mockery::mock(CustomerImporterInterface::class);
        $mockImporter->shouldReceive('import')
            ->once()
            ->with(50)
            ->andReturn(null);

        $this->app->instance(CustomerImporterInterface::class, $mockImporter);

        $job = new ImportCustomersJob(50);

        $job->handle($mockImporter);

        $this->assertTrue(true);
    }

    /**
     * Test job uses default count when none provided.
     */
    public function test_job_uses_default_count(): void
    {
        $mockImporter = Mockery::mock(CustomerImporterInterface::class);
        $mockImporter->shouldReceive('import')
            ->once()
            ->with(100)
            ->andReturn(null);

        $this->app->instance(CustomerImporterInterface::class, $mockImporter);

        $job = new ImportCustomersJob();

        $job->handle($mockImporter);

        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
