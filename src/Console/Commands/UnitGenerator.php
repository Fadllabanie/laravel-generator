<?php

namespace fadllabanie\laravel_unittest_generator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UnitGenerator extends Command
{
    protected $signature = 'generate:unittest {model} {modelPath} {--f|factory}';
    protected $description = 'Generate a unit test for a given model and factory class.';

    public function handle()
    {

        $modelName = $this->argument('model');
        $modelPath = $this->argument('modelPath');
        $modelClass = $modelPath . '\\' . $modelName;

        if ($this->option('factory')) {
            $this->generateFactory($modelName, $modelClass);
            $createTestContent   = $this->generateCreateTest($modelName);
            $readTestContent     = $this->generateReadTest($modelName);
            $updateTestContent   = $this->generateUpdateTest($modelName);
            $deleteTestContent   = $this->generateDeleteTest($modelName);
            $combinedTestContent = $this->generateTestClassWrapper($modelClass, $modelName, $createTestContent . $readTestContent . $updateTestContent . $deleteTestContent);

            $testPath = base_path("tests/Unit/{$modelName}Test.php");
            if (!file_exists($testPath)) {
                file_put_contents($testPath, $combinedTestContent);
                $this->info("Unit test generated for {$modelName}!");
            } else {
                $this->error("Test for {$modelName} already exists!");
            }
        } else {
            $testPath = base_path("database/factories/{$modelName}Factory.php");
            if (!file_exists($testPath)) {
                $this->info("Factory for {$modelName} not exists!");
            } else {

                $createTestContent   = $this->generateCreateTest($modelName);
                $readTestContent     = $this->generateReadTest($modelName);
                $updateTestContent   = $this->generateUpdateTest($modelName);
                $deleteTestContent   = $this->generateDeleteTest($modelName);
                $combinedTestContent = $this->generateTestClassWrapper($modelClass, $modelName, $createTestContent . $readTestContent . $updateTestContent . $deleteTestContent);

                $testPath = base_path("tests/Unit/{$modelName}Test.php");
                if (!file_exists($testPath)) {
                    file_put_contents($testPath, $combinedTestContent);
                    $this->info("Unit test generated for {$modelName}!");
                } else {
                    $this->error("Test for {$modelName} already exists!");
                }
            }
        }
    }

    protected function generateFactory($modelName, $modelClass)
    {
        $this->info("generateFactory -- {$modelName}!");

        if (!class_exists($modelClass)) {
            $this->error("Model $modelName does not exist!");
            return;
        }

        $model = new $modelClass;
        if (!method_exists($model, 'getFillable') || !method_exists($model, 'getFillableType')) {
            $this->error("Model $modelName must have both getFillable and getFillableType methods!");
            return;
        }


        $fillable = $model->getFillable();
        $fillableType = $model->getFillableType();

        $factoryContent = "<?php\n\nnamespace Database\Factories;\n\nuse $modelClass;\nuse Illuminate\Database\Eloquent\Factories\Factory;\nuse Faker\Generator as Faker;\n\nclass {$modelName}Factory extends Factory\n{\n    protected \$model = $modelName::class;\n\n    public function definition()\n    {\n        return [\n";

        foreach ($fillable as $field) {
            if (!isset($fillableType[$field])) {
                $this->error("No fillable type defined for $field!");
                return;
            }

            $type = $fillableType[$field];
            switch ($type) {
                case 'string':
                    $factoryContent .= "        '$field' => \$this->faker->word,\n";
                    break;
                case 'text':
                    $factoryContent .= "        '$field' => \$this->faker->text,\n";
                    break;
                case 'integer':
                    $factoryContent .= "        '$field' => \$this->faker->numberBetween(1, 1000),\n";
                    break;
                case 'float':
                    $factoryContent .= "        '$field' => \$this->faker->randomFloat(2, 0, 1000),\n";
                    break;
                case 'date':
                    $factoryContent .= "        '$field' => \Carbon\Carbon::now()->format('Y-m-d'),\n";
                    break;
                case 'datetime':
                    // $factoryContent .= "        '$field' => \$this->faker->dateTime(),\n";
                    $factoryContent .= "        '$field' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),\n";
                    break;
                case 'boolean':
                    $factoryContent .= "        '$field' => \$this->faker->boolean,\n";
                    break;
                case 'email':
                    $factoryContent .= "        '$field' => \$this->faker->safeEmail,\n";
                    break;
                case 'password':
                    $factoryContent .= "        '$field' => \$this->faker->password,\n";
                    break;
                case 'token':
                    $factoryContent .= "        '$field' => \Illuminate\Support\Str::random(60),\n";
                    break;
                case 'belongsTo':
                    $relatedModelName = ucfirst(str_replace('_id', '', $field));
                    $factoryContent .= "'{$field}' => \App\Models\\{$relatedModelName}::factory(),\n";
                    break;
            }
        }
        $factoryContent .= "    ];\n}\n}";

        $factoryPath = database_path("factories/{$modelName}Factory.php");
        file_put_contents($factoryPath, $factoryContent);

        $this->info("Factory created for $modelName!");
    }

    protected function generateCreateTest($modelName)
    {
        $this->info("create -- {$modelName}!");

        $snakeCaseModel = Str::snake($modelName);
        $tableName = Str::plural($snakeCaseModel);

        $columns = Schema::getColumnListing($tableName);
        $columns = array_diff($columns, ['id', 'created_at', 'updated_at']);
        $assertions = '';
        foreach ($columns as $column) {
            $assertions .= "\$this->assertEquals(\$data['{$column}'], \$model->{$column});\n            ";
        }

        return <<<EOD
            /** @test */
            public function it_can_create_a_{$snakeCaseModel}()
            {
                \$data = {$modelName}::factory()->make()->toArray();
                \$model = {$modelName}::create(\$data); 
                
                \$this->assertDatabaseHas('{$tableName}', \$data); 

                \$model = {$modelName}::find(\$model->id);
                \$this->assertNotNull(\$model); 
                {$assertions}

            }
            EOD;
    }

    protected function generateReadTest($modelName)
    {
        $this->info("read -- {$modelName}!");

        $snakeCaseModel = Str::snake($modelName);
        $tableName = Str::plural($snakeCaseModel);

        $columns = Schema::getColumnListing($tableName);
        $columns = array_diff($columns, ['id', 'created_at', 'updated_at']);
        $assertions = '';
        foreach ($columns as $column) {
            $assertions .= "\$this->assertEquals(\$originalData['{$column}'], \$retrievedModel->{$column});\n            ";
        }
        return <<<EOD
            /** @test */
            public function it_can_read_a_{$snakeCaseModel}()
            {
                \$originalData = {$modelName}::factory()->create()->toArray(); 
                \$retrievedModel = {$modelName}::find(\$originalData['id']); 
                
                \$this->assertNotNull(\$retrievedModel); 
                {$assertions}

            }
            EOD;
    }

    protected function generateUpdateTest($modelName)
    {
        $this->info("update -- {$modelName}!");

        $snakeCaseModel = Str::snake($modelName);
        $tableName = Str::plural($snakeCaseModel);

        $columns = Schema::getColumnListing($tableName);
        $columns = array_diff($columns, ['id', 'created_at', 'updated_at']);
        $assertions = '';
        foreach ($columns as $column) {
            $assertions .= "\$this->assertEquals(\$model['{$column}'], \$updatedModel->{$column});\n            ";
        }

        $modelClass = "App\\Models\\{$modelName}";
        if (!class_exists($modelClass)) {
            throw new \Exception("Model class {$modelClass} does not exist.");
        }
        $updateData = $modelClass::factory()->make()->only($columns);

        // $updateData = $modelName::factory()->make()->only($columns)->toArray();

        $updateDataString = var_export($updateData, true);

        return <<<EOD
            /** @test */
            public function it_can_update_a_{$snakeCaseModel}()
            {
                \$originalData = {$modelName}::factory()->create()->toArray(); 
                
                \$updateData = {$updateDataString};

                \$model = {$modelName}::find(\$originalData['id']); 
                \$model->update(\$updateData); 
            
                \$this->assertDatabaseHas('{$tableName}', \$updateData); 
            
                \$updatedModel = {$modelName}::find(\$model->id); 
                \$this->assertNotNull(\$updatedModel); 
                {$assertions}

            }
            EOD;
    }

    protected function generateDeleteTest($modelName)
    {
        $this->info("delete -- {$modelName}!");

        $snakeCaseModel = Str::snake($modelName);
        $tableName = Str::plural($snakeCaseModel);

        $columns = Schema::getColumnListing($tableName);
        $columns = array_diff($columns, ['id', 'created_at', 'updated_at']);
        $assertions = '';
        foreach ($columns as $column) {
            $assertions .= "\$this->assertEquals(\$data['{$column}'], \$model->{$column});\n            ";
        }
        return <<<EOD
        /** @test */
        public function it_can_delete_a_{$snakeCaseModel}()
        {
            \$data = {$modelName}::factory()->create()->toArray(); 
        
            \$model = {$modelName}::find(\$data['id']); 
            \$model->delete(); 
        
            \$this->assertDatabaseMissing('{$tableName}', \$data); 
            
            \$deletedModel = {$modelName}::find(\$data['id']); 
            \$this->assertNull(\$deletedModel); 
        }
        EOD;
    }


    protected function generateTestClassWrapper($modelClass, $modelName, $testMethods)
    {
        return <<<EOD
            <?php

            namespace Tests\Unit;

            use Tests\TestCase;
            use {$modelClass};
            use Illuminate\Foundation\Testing\RefreshDatabase;

            class {$modelName}Test extends TestCase
            {
                use RefreshDatabase;

                $testMethods
            }
            EOD;
    }
}
