<?php

namespace Tests\Feature;

use App\Contracts\CustomerImporterInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ImportCustomersCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test import command with default count.
     */
    public function test_import_command_with_default_count(): void
    {
        Http::fake([
            'https://randomuser.me/api/*' => Http::response(['results' => []], 200)
        ]);

        $this->artisan('import:customers')
            ->expectsOutput('Importing 100 Australian customers...')
            ->expectsOutput('Done.')
            ->assertExitCode(0);

        Http::assertSent(function ($request) {
            return $request['results'] === 100;
        });
    }

    /**
     * Test import command with custom count.
     */
    public function test_import_command_with_custom_count(): void
    {
        Http::fake([
            'https://randomuser.me/api/*' => Http::response(['results' => []], 200)
        ]);

        $this->artisan('import:customers', ['count' => 25])
            ->expectsOutput('Importing 25 Australian customers...')
            ->expectsOutput('Done.')
            ->assertExitCode(0);

        Http::assertSent(function ($request) {
            return $request['results'] === 25;
        });
    }

    /**
     * Test import command uses injected interface.
     */
    public function test_import_command_uses_injected_interface(): void
    {
        $mockImporter = \Mockery::mock(CustomerImporterInterface::class);
        $mockImporter->shouldReceive('import')
            ->once()
            ->with(10);

        $this->app->instance(CustomerImporterInterface::class, $mockImporter);

        $this->artisan('import:customers', ['count' => 10])
            ->assertExitCode(0);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
