<?php

namespace App\Console\Commands;

use App\Services\RandomUserImporter;
use Illuminate\Console\Command;

class ImportCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:customers {count=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import customers from randomuser.me';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        $this->info("Importing {$count} Australian customers...");
        
        // For testing, run directly. In production, you would dispatch a job:
        // \App\Jobs\ImportCustomersJob::dispatch($count);
        
        $importer = app(\App\Contracts\CustomerImporterInterface::class);
        $importer->import($count);
        
        $this->info('Done.');
    }
}
