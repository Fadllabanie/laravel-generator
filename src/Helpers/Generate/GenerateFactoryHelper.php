<?php

namespace fadllabanie\laravel_unittest_generator\Helpers\Generate;

use Illuminate\Support\Facades\Log;

class GenerateFactoryHelper
{
    public function generate(string $modelName, string $modelClass)
    {
        Log::Info("generateFactory -- {$modelName}!");

        if (!class_exists($modelClass)) {
            Log::Error("Model $modelName does not exist!");
            return;
        }

        $model = new $modelClass;
        if (!method_exists($model, 'getFillable') || !method_exists($model, 'getFillableType')) {
            Log::Error("Model $modelName must have both getFillable and getFillableType methods!");
            return;
        }


        $fillable = $model->getFillable();
        $fillableType = $model->getFillableType();

        $factoryContent = "<?php\n\nnamespace Database\Factories;\n\nuse $modelClass;\nuse Illuminate\Database\Eloquent\Factories\Factory;\nuse Faker\Generator as Faker;\n\nclass {$modelName}Factory extends Factory\n{\n    protected \$model = $modelName::class;\n\n    public function definition()\n    {\n        return [\n";

        foreach ($fillable as $field) {
            if (!isset($fillableType[$field])) {
                Log::Error("No fillable type defined for $field!");
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

        Log::Info("Factory created for $modelName!");
    }
}
