<?php

namespace App\Traits;

use Exception;
use fadllabanie\laravel_unittest_generator\Models\ErrorLog;

trait HandlesErrors
{
    /**
     * Execute a operation with error handling.
     *
     * @param  \Closure $operation
     * @param  string   $action
     * @return mixed
     */
    public function executeCrudOperation(\Closure $operation, string $action)
    {
        try {
            return $operation();
        } catch (Exception $e) {
            $this->logError($e, $action);
            // If you need to do something specific like redirecting to a page or returning a custom response, do it here.
            throw $e;
        }
    }

    /**
     * Log an error to the database.
     *
     * @param  Exception $exception
     * @param  string    $action
     * @return void
     */
    protected function logError(Exception $exception, string $action): void
    {
        ErrorLog::create([
            'action'          => $action,
            'message'         => $exception->getMessage(),
            'exception_stack' => $exception->getTraceAsString(),
            'context'         => json_encode(request()->all())
        ]);
    }
}
