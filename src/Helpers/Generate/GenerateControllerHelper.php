<?php

namespace fadllabanie\laravel_unittest_generator\Helpers\Generate;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateControllerHelper
{
    public function generate(string $modelName, string $modelClass)
    {

        $this->generateCreateAction($modelName);
        $this->generateReadAllAction($modelName);
        $this->generateReadAction($modelName);
        $this->generateUpdateAction($modelName);
        $this->generateDeleteAction($modelName);
        $this->generateControllerAction($modelName);
    }

    protected function generateCreateAction($modelName)
    {
        $modelNameStudly = Str::studly($modelName);

        $modelNamePlural = Str::plural($modelName);
        $actionNamespace = 'App\\Actions\\' . $modelNamePlural;

        $actionClass = "Create{$modelNameStudly}Action";

        $modelNamePlural = Str::plural($modelNameStudly);
        $actionPath = app_path("Actions/{$modelNamePlural}/{$actionClass}.php");

        if (File::exists($actionPath)) {
            throw new \Exception("The action file {$actionClass}.php already exists.");
        }

        $actionContent = <<<PHP
        <?php

        namespace {$actionNamespace};

        use App\Models\\{$modelNameStudly};

        class {$actionClass}
        {
            public function execute(array \$data): {$modelNameStudly}
            {
                // Validate and create a new {$modelNameStudly}
                // TODO: Add your validation and creation logic here

                return {$modelNameStudly}::create(\$data);
            }
        }

        PHP;

        File::ensureDirectoryExists(app_path('Actions'));

        $directory = dirname($actionPath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true); // true for recursive create
        }

        File::put($actionPath, $actionContent);

        Log::info("{$actionClass}.php generated successfully.\n");
    }
    protected function generateUpdateAction($modelName)
    {
        $modelNameStudly = Str::studly($modelName);

        $modelNamePlural = Str::plural($modelName);
        $actionNamespace = 'App\\Actions\\' . $modelNamePlural;

        $actionClass = "Update{$modelNameStudly}Action";

        $modelNamePlural = Str::plural($modelNameStudly);
        $actionPath = app_path("Actions/{$modelNamePlural}/{$actionClass}.php");


        if (File::exists($actionPath)) {
            throw new \Exception("The action file {$actionClass}.php already exists.");
        }

        $actionContent = <<<PHP
<?php

namespace {$actionNamespace};

use App\Models\\{$modelNameStudly};

class {$actionClass}
{
    public function execute({$modelNameStudly} \$model, array \$data): {$modelNameStudly}
    {

        \$model->update(\$data);

        return \$model;
    }
}

PHP;

        File::ensureDirectoryExists(app_path('Actions'));
        File::put($actionPath, $actionContent);
    }

    protected function generateDeleteAction($modelName)
    {
        $modelNameStudly = Str::studly($modelName);


        $modelNamePlural = Str::plural($modelName);
        $actionNamespace = 'App\\Actions\\' . $modelNamePlural;

        $actionClass = "Delete{$modelNameStudly}Action";

        $modelNamePlural = Str::plural($modelNameStudly);
        $actionPath = app_path("Actions/{$modelNamePlural}/{$actionClass}.php");

        if (File::exists($actionPath)) {
            throw new \Exception("The action file {$actionClass}.php already exists.");
        }

        $actionContent = <<<PHP
    <?php
    
    namespace {$actionNamespace};
    
    use App\Models\\{$modelNameStudly};
    
    class {$actionClass}
    {
        public function execute({$modelNameStudly} \$model): bool
        {
    
            return \$model->delete();
        }
    }
    
    PHP;

        File::ensureDirectoryExists(app_path('Actions'));
        File::put($actionPath, $actionContent);
    }

    protected function generateReadAllAction($modelName)
    {
        $modelNameStudly = Str::studly($modelName);
        $actionClass = "GetAll{$modelNameStudly}Action";


        $modelNamePlural = Str::plural($modelName);
        $actionNamespace = 'App\\Actions\\' . $modelNamePlural;


        $modelNamePlural = Str::plural($modelNameStudly);
        $actionPath = app_path("Actions/{$modelNamePlural}/{$actionClass}.php");

        if (File::exists($actionPath)) {
            throw new \Exception("The action file {$actionClass}.php already exists.");
        }

        $actionContent = <<<PHP
<?php

    namespace {$actionNamespace};

use App\Models\\{$modelNameStudly};

class {$actionClass}
{
    public function execute()
    {
        return {$modelNameStudly}::all();
    }
}

PHP;

        File::ensureDirectoryExists(app_path('Actions'));
        File::put($actionPath, $actionContent);
    }
    protected function generateReadAction($modelName)
    {
        $modelNameStudly = Str::studly($modelName);
        $actionClass = "Get{$modelNameStudly}Action";


        $modelNamePlural = Str::plural($modelName);
        $actionNamespace = 'App\\Actions\\' . $modelNamePlural;


        $modelNamePlural = Str::plural($modelNameStudly);
        $actionPath = app_path("Actions/{$modelNamePlural}/{$actionClass}.php");


        if (File::exists($actionPath)) {
            throw new \Exception("The action file {$actionClass}.php already exists.");
        }

        $actionContent = <<<PHP
<?php

    namespace {$actionNamespace};

use App\Models\\{$modelNameStudly};

class {$actionClass}
{
    public function execute(int \$id): ?{$modelNameStudly}
    {
        return {$modelNameStudly}::find(\$id);
    }
}

PHP;

        File::ensureDirectoryExists(app_path('Actions'));
        File::put($actionPath, $actionContent);
    }

    protected function generateControllerAction($modelName)
    {
        $modelNameStudly = Str::studly($modelName);
        $modelNamePlural = Str::plural($modelName);

        $controllerClass = "{$modelNameStudly}Controller";
        $controllerPath = app_path("Http/Controllers/{$controllerClass}.php");

        if (File::exists($controllerPath)) {
            throw new \Exception("The controller file {$controllerClass}.php already exists.");
        }

        $actionContent = <<<PHP
    <?php
    
    namespace App\Http\Controllers;
    
    use App\Http\Requests\\Store{$modelNameStudly}Request;
    use App\Http\Requests\\Update{$modelNameStudly}Request;
    use App\Actions\\{$modelNamePlural}\\Create{$modelNameStudly}Action;
    use App\Actions\\{$modelNamePlural}\\Update{$modelNameStudly}Action;
    use App\Actions\\{$modelNamePlural}\\Delete{$modelNameStudly}Action;
    use App\Actions\\{$modelNamePlural}\\GetAll{$modelNameStudly}Action;
    use App\Actions\\{$modelNamePlural}\\Get{$modelNameStudly}Action;
    use App\Models\\{$modelNameStudly};
    use App\Traits\HandlesErrors;

    class {$controllerClass} extends Controller
    {
        use HandlesErrors;

        public function index(GetAll{$modelNameStudly}Action \$action)
        {
            return \$this->executeCrudOperation(function () use (\$action) {
                \$models = \$action->execute();
                return response()->json(\$models);
            }, 'index'); 
        }
    
        public function store(Store{$modelNameStudly}Request \$request, Create{$modelNameStudly}Action \$action)
        {
            return \$this->executeCrudOperation(function () use (\$request, \$action) {
                \$model = \$action->execute(\$request->validated());
                return response()->json(\$model, 201);
            }, 'store');
        }
    
        public function show(\$id, Get{$modelNameStudly}Action \$action)
        {
            return \$this->executeCrudOperation(function () use (\$id, \$action) {
                \$model = \$action->execute(\$id);
                if (!\$model) {
                    return response()->json(['error' => 'Not Found'], 404);
                }
                
                return response()->json(\$model);
            }, 'show');
        }
    
        public function update(Update{$modelNameStudly}Request \$request, \$id, Update{$modelNameStudly}Action \$action)
        {
            return \$this->executeCrudOperation(function () use (\$request, \$id, \$action) {
                \$model = {$modelNameStudly}::findOrFail(\$id);
                \$action->execute(\$model,\$request->validated());
                return response()->json(\$model);
            }, 'update');

        }
    
        public function destroy(\$id, Delete{$modelNameStudly}Action \$action)
        {
            return \$this->executeCrudOperation(function () use (\$id, \$action) {
                \$model = {$modelNameStudly}::findOrFail(\$id);
                \$action->execute(\$model);
                return response()->json(null, 204);
            }, 'destroy');
        }
    }
    
    PHP;

        File::ensureDirectoryExists(app_path('Http/Controllers'));
        File::put($controllerPath, $actionContent);

        echo "{$controllerClass}.php generated successfully.\n";
    }
}
