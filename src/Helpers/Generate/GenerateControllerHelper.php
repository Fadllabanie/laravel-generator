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

        $actionNamespace = 'App\\Actions';
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
        $actionClass = "Update{$modelNameStudly}Action";

        $modelNamePlural = Str::plural($modelNameStudly);
        $actionPath = app_path("Actions/{$modelNamePlural}/{$actionClass}.php");


        if (File::exists($actionPath)) {
            throw new \Exception("The action file {$actionClass}.php already exists.");
        }

        $actionContent = <<<PHP
<?php

namespace App\Actions;

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
        $actionClass = "Delete{$modelNameStudly}Action";

        $modelNamePlural = Str::plural($modelNameStudly);
        $actionPath = app_path("Actions/{$modelNamePlural}/{$actionClass}.php");

        if (File::exists($actionPath)) {
            throw new \Exception("The action file {$actionClass}.php already exists.");
        }

        $actionContent = <<<PHP
    <?php
    
    namespace App\Actions;
    
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

        $modelNamePlural = Str::plural($modelNameStudly);
        $actionPath = app_path("Actions/{$modelNamePlural}/{$actionClass}.php");

        if (File::exists($actionPath)) {
            throw new \Exception("The action file {$actionClass}.php already exists.");
        }

        $actionContent = <<<PHP
<?php

namespace App\Actions;

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

        $modelNamePlural = Str::plural($modelNameStudly);
        $actionPath = app_path("Actions/{$modelNamePlural}/{$actionClass}.php");


        if (File::exists($actionPath)) {
            throw new \Exception("The action file {$actionClass}.php already exists.");
        }

        $actionContent = <<<PHP
<?php

namespace App\Actions;

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
    use App\Actions\Create{$modelNameStudly}Action;
    use App\Actions\Update{$modelNameStudly}Action;
    use App\Actions\Delete{$modelNameStudly}Action;
    use App\Actions\GetAll{$modelNameStudly}Action;
    use App\Actions\Get{$modelNameStudly}Action;
    use App\Models\\{$modelNameStudly};
    
    class {$controllerClass} extends Controller
    {
        public function index(GetAll{$modelNameStudly}Action \$action)
        {
            \$models = \$action->execute();
            return response()->json(\$models);
        }
    
        public function store(Store{$modelNameStudly}Request \$request, Create{$modelNameStudly}Action \$action)
        {
            \$model = \$action->execute(\$request->validated());
            return response()->json(\$model, 201);
        }
    
        public function show(\$id, Get{$modelNameStudly}Action \$action)
        {
            \$model = \$action->execute(\$id);
            return \$model ? response()->json(\$model) : response()->json(['error' => 'Not Found'], 404);
        }
    
        public function update(Update{$modelNameStudly}Request \$request, \$id, Update{$modelNameStudly}Action \$action)
        {
            \$model = {$modelNameStudly}::findOrFail(\$id);
            \$action->execute(\$model, \$request->validated());
            return response()->json(\$model);
        }
    
        public function destroy(\$id, Delete{$modelNameStudly}Action \$action)
        {
            \$model = {$modelNameStudly}::findOrFail(\$id);
            \$action->execute(\$model);
            return response()->json(null, 204);
        }
    }
    
    PHP;

        File::ensureDirectoryExists(app_path('Http/Controllers'));
        File::put($controllerPath, $actionContent);

        echo "{$controllerClass}.php generated successfully.\n";
    }
}
