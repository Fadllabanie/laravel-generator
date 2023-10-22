<?php

namespace fadllabanie\laravel_unittest_generator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UnitGenerator extends Command
{
    protected $signature = 'generate:unittest {model}';
    protected $description = 'Generate a unit test for a given model and factory class.';

    public function handle()
    {

        $modelName = $this->argument('model');
        $modelClass = "App\\Models\\$modelName";

        $this->generateFactory($modelName);

        $createTestContent   = $this->generateCreateTest($modelName);
        $readTestContent     = $this->generateReadTest($modelName);
        $updateTestContent   =  $this->generateUpdateTest($modelName);
        $deleteTestContent   = $this->generateDeleteTest($modelName);
        $combinedTestContent = $this->generateTestClassWrapper($modelClass, $modelName, $createTestContent . $readTestContent . $updateTestContent . $deleteTestContent);

        // Write to the test file
        $testPath = base_path("tests/Unit/{$modelName}Test.php");
        if (!file_exists($testPath)) {
            file_put_contents($testPath, $combinedTestContent);
            $this->info("Unit test generated for {$modelName}!");
        } else {
            $this->error("Test for {$modelName} already exists!");
        }
    }

    protected function generateFactory($modelName)
    {
        $this->info("generateFactory -- {$modelName}!");
        $modelClass = "App\\Models\\$modelName";
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

        // Start building the factory content
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
                    $factoryContent .= "        '$field' => \$this->faker->date(),\n";
                    break;
                case 'datetime':
                    $factoryContent .= "        '$field' => \$this->faker->dateTime(),\n";
                    break;
                case 'boolean':
                    $factoryContent .= "        '$field' => \$this->faker->boolean,\n";
                    break;
                case 'email':
                    $factoryContent .= "        '$field' => \$this->faker->safeEmail,\n";
                    break;
            }
        }
        $factoryContent .= "    ];\n}\n}";

        // Write to the appropriate factory file
        $factoryPath = database_path("factories/{$modelName}Factory.php");
        file_put_contents($factoryPath, $factoryContent);

        $this->info("Factory created for $modelName!");
    }

    protected function generateCreateTest($modelName)
    {
        $this->info("create -- {$modelName}!");

        $snakeCaseModel = Str::snake($modelName); // Convert to snake case (e.g., 'Post' becomes 'post')
        $tableName = Str::plural($snakeCaseModel); // Convert to plural (e.g., 'Post' becomes 'posts')

        return <<<EOD
            /** @test */
            public function it_can_create_a_{$snakeCaseModel}()
            {
                \$data = {$modelName}::factory()->make()->toArray(); // Generates data but doesn't save to DB.
                \$modelInstance = {$modelName}::create(\$data); // Creates model instance with the data and saves to DB.
                
                \$this->assertDatabaseHas('{$tableName}', \$data); // Check if the data exists in the database.

                \$model = {$modelName}::find(\$modelInstance->id); // Retrieve model from DB.
                \$this->assertNotNull(\$model); // Assert that we did indeed find a model.
                \$this->assertEquals(\$data['title'], \$model->title); // Just a sample assertion for 'title'. Repeat for other fields as needed.
            }
            EOD;
    }

    protected function generateReadTest($modelName)
    {
        $this->info("read -- {$modelName}!");

        $snakeCaseModel = Str::snake($modelName); // Convert to snake case (e.g., 'Post' becomes 'post')
        $tableName = Str::plural($snakeCaseModel); // Convert to plural (e.g., 'Post' becomes 'posts')

        return <<<EOD
            /** @test */
            public function it_can_read_a_{$snakeCaseModel}()
            {
                \$originalData = {$modelName}::factory()->create()->toArray(); // Creates model instance with data and saves to DB.

                \$retrievedModel = {$modelName}::find(\$originalData['id']); // Retrieve the model from the database.
                
                \$this->assertNotNull(\$retrievedModel); // Assert that we did indeed find a model.
                \$this->assertEquals(\$originalData['title'], \$retrievedModel->title); // Just a sample assertion for 'title'. Repeat for other fields as needed.
            }
            EOD;
    }

    protected function generateUpdateTest($modelName)
    {
        $this->info("update -- {$modelName}!");

        $snakeCaseModel = Str::snake($modelName); // Convert to snake case (e.g., 'Post' becomes 'post')
        $tableName = Str::plural($snakeCaseModel); // Convert to plural (e.g., 'Post' becomes 'posts')

        return <<<EOD
            /** @test */
            public function it_can_update_a_{$snakeCaseModel}()
            {
                \$originalData = {$modelName}::factory()->create()->toArray(); // Creates model instance with data and saves to DB.
                
                // Sample update data. Modify this as per your requirements. Maybe use another factory state.
                \$updateData = [
                    'title' => 'Updated Title',
                    'description' => 'Updated Description'
                ];
                
                \$modelInstance = {$modelName}::find(\$originalData['id']); // Fetch the model from the database.
                \$modelInstance->update(\$updateData); // Update the model with new data.
            
                \$this->assertDatabaseHas('{$tableName}', \$updateData); // Check if the updated data exists in the database.
            
                \$updatedModel = {$modelName}::find(\$modelInstance->id); // Retrieve the updated model from DB.
                \$this->assertNotNull(\$updatedModel); // Assert that we did indeed find the model.
                \$this->assertEquals(\$updateData['title'], \$updatedModel->title); // Assertion for 'title'. Repeat for other fields as needed.
            }
            EOD;
    }

    protected function generateDeleteTest($modelName)
    {
        $this->info("delete -- {$modelName}!");

        $snakeCaseModel = Str::snake($modelName); // Convert to snake case (e.g., 'Post' becomes 'post')
        $tableName = Str::plural($snakeCaseModel); // Convert to plural (e.g., 'Post' becomes 'posts')

        return <<<EOD
        /** @test */
        public function it_can_delete_a_{$snakeCaseModel}()
        {
            \$data = {$modelName}::factory()->create()->toArray(); // Creates model instance with data and saves to DB.
        
            \$modelInstance = {$modelName}::find(\$data['id']); // Fetch the model from the database.
            \$modelInstance->delete(); // Delete the model.
        
            \$this->assertDatabaseMissing('{$tableName}', \$data); // Check if the data was deleted from the database.
            
            \$deletedModel = {$modelName}::find(\$data['id']); // Try to retrieve the deleted model from DB.
            \$this->assertNull(\$deletedModel); // Assert that the model was indeed deleted.
        }
        EOD;
    }

    // This method wraps all test methods inside a class
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
