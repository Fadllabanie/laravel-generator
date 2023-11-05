<?php

namespace fadllabanie\laravel_unittest_generator\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    protected $fillable = ['action', 'message', 'exception_stack', 'context'];
}
