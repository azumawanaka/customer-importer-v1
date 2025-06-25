<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name : The name of the service class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * The filesystem instance.
     */
    protected $files;

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $className = Str::studly($name);
        
        // Create Services directory if it doesn't exist
        $servicesPath = app_path('Services');
        if (!$this->files->isDirectory($servicesPath)) {
            $this->files->makeDirectory($servicesPath, 0755, true);
        }
        
        $filePath = $servicesPath . '/' . $className . '.php';
        
        if ($this->files->exists($filePath)) {
            $this->error('Service already exists!');
            return 1;
        }
        
        $stub = $this->getStub();
        $content = str_replace('{{className}}', $className, $stub);
        
        $this->files->put($filePath, $content);
        
        $this->info('Service created successfully: ' . $filePath);
        
        return 0;
    }
    
    /**
     * Get the stub content for the service class.
     */
    protected function getStub()
    {
        return <<<'STUB'
<?php

namespace App\Services;

class {{className}}
{
    /**
     * Create a new service instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the service logic.
     */
    public function handle()
    {
        //
    }
}
STUB;
    }
}
