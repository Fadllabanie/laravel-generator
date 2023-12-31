<?php

namespace fadllabanie\laravel_unittest_generator\Helpers\Generate;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GenerateUnitTestHelper
{
    public function generate(string $modelName, string $modelClass)
    {

        $createTestContent   = $this->generateCreateTest($modelName);
        $readTestContent     = $this->generateReadTest($modelName);
        $updateTestContent   = $this->generateUpdateTest($modelName);
        $deleteTestContent   = $this->generateDeleteTest($modelName);
        $combinedTestContent = $this->generateTestClassWrapper($modelClass, $modelName, $createTestContent . $readTestContent . $updateTestContent . $deleteTestContent);

        $testPath = base_path("tests/Unit/{$modelName}Test.php");

        if (!file_exists($testPath)) {
            file_put_contents($testPath, $combinedTestContent);
            Log::info("Unit test generated for {$modelName}!");
        } else {
            Log::error("Test for {$modelName} already exists!");
        }
    }

    protected function generateCreateTest($modelName)
    {
        Log::info("create -- {$modelName}!");

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
        Log::info("read -- {$modelName}!");

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
        Log::info("update -- {$modelName}!");

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
        $factoryData = '';

        $updateData = $modelClass::factory()->make()->only($columns);

        // $updateData = $modelName::factory()->make()->only($columns)->toArray();

        $modelInstance = new $modelClass;
        $relations = method_exists($modelInstance, 'getRelationData') ? $modelInstance->getRelationData() : [];

        foreach ($relations as $relation => $relationModel) {
            $relatedModelInstance = $relationModel::factory()->create();
            $factoryData .= "\$$relation = $relationModel::factory()->create(); // Create a new $relation\n        ";
            $updateData[$relation . '_id'] = '$' . $relation . '->id';
        }

        $updateDataString = var_export($updateData, true);

        return <<<EOD
            /** @test */
            public function it_can_update_a_{$snakeCaseModel}()
            {
                \$originalData = {$modelName}::factory()->create()->toArray(); 
                $factoryData

                \$updateData = $updateDataString;

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
        Log::info("delete -- {$modelName}!");

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
