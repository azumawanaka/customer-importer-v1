<?php

namespace App\Jobs;

use App\Contracts\CustomerImporterInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ImportCustomersJob implements ShouldQueue
{
    use Queueable;

    /**
     * @var int
     */
    private int $count;

    /**
     * Create a new job instance.
     */
    public function __construct(int $count = 100)
    {
        $this->count = $count;
    }

    /**
     * Execute the job.
     */
    public function handle(CustomerImporterInterface $importer): void
    {
        $importer->import($this->count);
    }
}
