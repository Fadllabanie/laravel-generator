# Laravel Unit Test Generator

Easily generate unit tests for your Laravel applications with the `laravel_unittest_generator` package.

## Installation

To install via Composer:

```bash
composer require fadllabanie/laravel_unittest_generator:dev-main
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

Where datatype should match one of the supported data types:
`string`, `text`, `integer`, `float`, `date`, `datetime`, `boolean`,`password`, `email`,`token`,`belongsTo`

## Usage

Once installed, you can generate unit tests for your models with the provided Artisan command:

```bash
php artisan generate:unittest {model} {modelPath} {options}
```

Replace {model} with the actual name of your Eloquent model, and {modelPath} with the full namespace path where your model resides. It's as easy as asking your application to kindly roll out tests for the specified model.


## Options
Fine-tune your unit test generation with these enchanting flags:

`-f` or --factory: Summon a factory alongside your test to ensure your database is seeded with data that mimics your production models.

`-c` or --controller: This potent flag will not only craft a unit test but will also weave a controller test into existence, doubling your testing prowess.

`-r` or --request: Implement this flag to create tests that safeguard your HTTP requests, ensuring they're fortified and secure.



## Example:
```bash
php artisan generate:unittest Post App\\Models -f -r -c
```

## Features:

Generate Factories:
This package automatically generates Laravel factories based on the model's $fillable and $fillableType properties.

## Generate CRUD Unit Tests:

With a single command, you can generate unit tests for Create, Read, Update, and Delete operations for any given model also belongs to relationship

## Generate Factory

In a developer's toolkit, the ability to swiftly create mock objects for testing is like possessing a philosopher's stone that turns base metal into gold. Factories in Laravel serve this alchemical purpose by providing a set of blueprints for generating testable data. Our command assists in transmuting your testing process by generating dedicated factory classes for your Eloquent models.

### Why Factories?

- **Efficiency**: Quickly generate large volumes of database records for testing purposes.
- **Convenience**: Define once, use everywhere – set up your model factories and you can spin up objects in all your tests with ease.
- **Consistency**: Ensure that your tests are using data that conforms to the application's expectations and validations.

### What You Get:

Upon invoking our generate factory command, you are graced with:

- A model factory tailored to produce items mimicking real database records.
- A definition method that outlines the default state for your model.
- Optional states to create varied scenarios for more comprehensive testing.

### Command Usage:

To manifest a factory for your model, employ the following artisan command:

```bash
php artisan generate:unittest {model} {modelPath} -f
```


## Generate Request Validation:
Step up your testing game by automatically generating Request Validation tests for your Laravel application. Our tool understands the importance of bulletproof validations in your HTTP requests, ensuring that only valid data makes its way through your application's endpoints.

### What It Does:

By generating Request Validation tests, you ensure that:

- Your application adheres to the specified validation rules.
- Invalid data is promptly caught and handled gracefully.
- Your API endpoints remain robust and secure against malformed requests.

Testing request validations can often be repetitive and mundane, but with our command, you can generate these tests with precision and efficiency, saving you countless hours of manual testing.

### How to Use:

To conjure Request Validation tests for your model, utilize the following command:

```bash
php artisan generate:unittest {model} {modelPath} -r
```

## Generate Controller Using the Action Pattern

Dive into the realm of the Action pattern, a powerful paradigm that encapsulates individual requests' business logic into their own distinct classes. This approach promotes clean, maintainable, and reusable code. It enables you to handle complex user interactions with elegance and ease, ensuring that each aspect of the request's processing is neatly segmented and straightforward to navigate.

### What Is the Action Pattern?

The Action pattern takes a cue from the Single Responsibility Principle, one of the SOLID design principles. It suggests that each class should handle a single part of the functionality provided by the software, and it should encapsulate that part entirely. In the context of HTTP controllers, this means breaking down the typical CRUD operations into separate Action classes, each responsible for a single aspect of the CRUD functionality.

### Advantages of the Action Pattern:

- **Clarity**: Each Action class is a transparent container for the process it represents, making the flow of logic easy to understand.
- **Reusability**: Action classes can be reused across different controllers or even different applications.
- **Testability**: Isolating logic into discrete classes makes unit testing more straightforward and focused.
- **Maintainability**: Changes to the application’s behavior can be managed with minimal impact on the rest of the system.

### Classes Added by the Command:

When you invoke the controller generation command, it creates a suite of Action classes that represent the core operations of your model's controller:

- `Create{Model}Action` for handling creation logic.
- `Read{Model}Action` for retrieving a single model instance.
- `ReadAll{Model}Action` for listing all instances of a model.
- `Update{Model}Action` for updating a given model.
- `Delete{Model}Action` for removing a model from the database.

Each class is meticulously crafted to manage only its designated task, ensuring that your controllers remain lightweight and your business logic remains pristine.

### How to Generate:

To create a new controller equipped with the Action pattern, use the following artisan command:

```bash
php artisan generate:unittest {model} {modelPath} -c
```

## Known Issues:

Make sure that your models are correctly namespaced and that you've correctly set up your autoload paths in your composer.json file.
Ensure that your models have the necessary `$fillable` and `$fillableType` properties defined for the package to work correctly.

Ensure that your models have the necessary `getFillable()` and `getFillableType()` properties defined for the package to work correctly.

## Contributing:

We welcome contributions! Please submit PRs for any enhancements, fixes, or features you want to add.
