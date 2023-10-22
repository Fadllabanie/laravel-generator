# Laravel Unit Test Generator

Easily generate unit tests for your Laravel applications with the `laravel_unittest_generator` package.

## Installation

To install via Composer:

```bash
composer require fadllabanie/laravel_unittest_generator
```


## Configuration:
To properly use this package, your models should contain the following properties:
```bash
public function getFillable()
    {
        return [
            'title',
            'description',
        ];
    }
```
```bash
public function getFillableType()
    {
        return [
            'title' => 'string',
            'description' => 'text',
        ];
    }
```
Where datatype should match one of the supported data types: `string`, `text`, `integer`, `float`, `date`, `datetime`, `boolean`, `email`.



## Usage
Once installed, you can generate unit tests for your models with the provided Artisan command:
```bash
php artisan generate:unittest ModelName
```
## Features:
Generate Factories:
This package automatically generates Laravel factories based on the model's $fillable and $fillableType properties.

## CRUD Unit Tests:
With a single command, you can generate unit tests for Create, Read, Update, and Delete operations for any given model.

## Known Issues:
Make sure that your models are correctly namespaced and that you've correctly set up your autoload paths in your composer.json file.
Ensure that your models have the necessary `$fillable` and `$fillableType` properties defined for the package to work correctly.

Ensure that your models have the necessary `getFillable()` and `getFillableType()` properties defined for the package to work correctly.

## Contributing:
We welcome contributions! Please submit PRs for any enhancements, fixes, or features you want to add.

