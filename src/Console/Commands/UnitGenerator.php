<?php

namespace fadllabanie\laravel_unittest_generator\Console\Commands;

use fadllabanie\laravel_unittest_generator\Helpers\Generate\GenerateFactoryHelper;
use fadllabanie\laravel_unittest_generator\Helpers\Generate\GenerateRequestHelper;
use fadllabanie\laravel_unittest_generator\Helpers\Generate\GenerateUnitTestHelper;
use Illuminate\Console\Command;

class UnitGenerator extends Command
{
    protected $signature = 'generate:unittest {model} {modelPath} {--f|factory} {--r|request}';
    protected $description = 'Generate a unit test for a given model and factory class php artisan generate:unittest Post App\\Models -f -r.';

    public function handle()
    {
        $modelName = $this->argument('model');
        $modelPath = $this->argument('modelPath');
        $modelClass = $modelPath . '\\' . $modelName;

        if ($this->option('request')) {
            $helper = new GenerateRequestHelper();
            $helper->generate($modelName, $modelClass);
        }
        if ($this->option('factory')) {
            $helper = new GenerateFactoryHelper();
            $helper->generate($modelName, $modelClass);
        }
        $testPath = base_path("database/factories/{$modelName}Factory.php");
        if (!file_exists($testPath)) {
            $this->info("Factory for {$modelName} not exists!");
        } else {

            $helper = new GenerateUnitTestHelper();
            $helper->generate($modelName, $modelClass);
        }
    }
}
