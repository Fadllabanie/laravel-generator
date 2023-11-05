# Laravel Unit Test Generator

Supercharge your Laravel application's testing workflow with the `laravel_unittest_generator` package. This dynamic toolkit is your steadfast ally in creating robust unit tests with a touch of magic!

## 🚀 Installation

Time to bring the `laravel_unittest_generator` into your project. Cast these spells with Composer:

```bash
composer require fadllabanie/laravel_unittest_generator
```

Invoke the artisan to publish your configs:

```bash
php artisan vendor:publish
```

Finally, migrate to shape your database's destiny:

```bash
php artisan migrate
```

## 🔧 Configuration

For the enchantment to work, your models must be imbued with these arcane properties:

```php
public function getFillable()
{
    return [
        'title',
        'description',
    ];
}

public function getFillableType()
{
    return [
        'title' => 'string',
        'description' => 'text',
    ];
}
```

Supported data types include: `string`, `text`, `integer`, `float`, `date`, `datetime`, `boolean`, `password`, `email`, `token`, `belongsTo`

## 📖 Usage

With the package now part of your repository, conjure unit tests for your models with a simple artisan command:

```bash
php artisan generate:unittest {Model} {ModelPath} {options}
```

## 🎛️ Options

Fine-tune your unit test generation with these options:

- `-f` or `--factory`: Summon a factory alongside your test for authentic data alchemy.
- `-c` or `--controller`: Invoke a controller test for double the sorcery.
- `-r` or `--request`: Enforce request validation tests for an impenetrable defense.

## ✨ Features

- **Generate Factories**: Automatically create factories based on your model's `$fillable` and `$fillableType`.
- **CRUD Unit Tests**: Conjure tests for Create, Read, Update, Delete, and relationships in one incantation.
- **Action Pattern Controller**: Organize your controller logic with discrete Action classes for clarity and reusability.

## 🧪 Generate Factories

The command transmutes your model properties into a factory class, paving the way for effortlessly creating test data:

```bash
php artisan generate:unittest {Model} {ModelPath} -f
```

## 🔒 Generate Request Validation Tests

Strengthen your application's defenses by automating Request Validation tests:

```bash
php artisan generate:unittest {Model} {ModelPath} -r
```

## 🛠️ Generate Controller with Action Pattern

Embrace clean architecture by splitting controller logic into dedicated Action classes:

```bash
php artisan generate:unittest {Model} {ModelPath} -c
```

## 💡 The Magic Behind the Trait

Our Error Logging Trait is your silent guardian, logging every misstep in the shadows so your app never falters.

### Benefits

- **Automated Error Tracking**: Capture errors without lifting a wand.
- **Easy Error Retrieval**: Consult the database as your crystal ball for error insights.
- **Streamlined Debugging**: Navigate the maze of bugs with an enchanted map.

### How to Generate

To arm your controllers with our trait, simply integrate it with:

```bash
php artisan generate:unittest {Model} {ModelPath} -f -r -c
```

## 📜 Known Issues

Ensure your models are correctly named and that your `composer.json` pathways are prophesied correctly. The `$fillable` and `$fillableType` must be declared to conjure tests properly.

## 📚 Contributing

Join our coven of contributors! Whether it's fixes, enhancements, or new features, your pull requests shall be greeted with a feast.

---

This README not only guides users through the mystical arts of unit test generation but also conveys the magical essence of the package. It aims to engage developers by blending technical instructions with thematic charm.