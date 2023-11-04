<?php

namespace fadllabanie\laravel_unittest_generator\Helpers\Generate;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class GenerateRequestHelper
{
    public function generate(string $modelName, string $modelClass)
    {
        $model = $modelName;

        Artisan::call('make:request', ['name' => "Store{$model}Request"]);
        Log::info("Store{$model}Request created successfully.");

        Artisan::call('make:request', ['name' => "Update{$model}Request"]);
        Log::info("Update{$model}Request created successfully.");

        // Auto-fill request details if needed...
        $this->populateRequestDetails($model, $modelClass);
    }

    private function populateRequestDetails($modelName, $modelClassPath)
    {
        $modelPath = $modelClassPath;
        $modelClass = $modelPath;

        if (!class_exists($modelClass)) {
            Log::error("The model {$modelClass} does not exist.");
            return;
        }

        $modelInstance = new $modelClass;  // This will create an instance of the model.


        $fillable = $modelInstance->getFillable();
        $fillableTypes = $modelInstance->getFillableType();

        $validationRules = [];

        foreach ($fillable as $attribute => $type) {

            $validationRules[$type] = 'required|' . $fillableTypes[$type];
        }

        $this->writeRulesToRequestClass("Store{$modelName}Request", $validationRules);
        $this->writeRulesToRequestClass("Update{$modelName}Request", $validationRules);
    }


    private static function writeRulesToRequestClass($requestClassName, $rules)
    {
        $rulesStringRepresentation = var_export($rules, true);

        $path = app_path("Http/Requests/{$requestClassName}.php");

        if (class_exists($path)) {
            Log::error("The model {$path} is exist.");
            return;
        }

        $pattern = '/public function rules\(\)\s*: array\s*{\s*return \[\s*\/\/\s*\];\s*}/s';
        $contents = file_get_contents($path);

        if (!preg_match($pattern, $contents)) {
            dd("Pattern does not match in file: {$path}");
        }

        $contents = preg_replace($pattern, "public function rules() {\n\treturn {$rulesStringRepresentation};\n}", $contents);

        file_put_contents($path, $contents);
    }
}
