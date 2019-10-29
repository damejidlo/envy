Envy
====

[![Build Status](https://travis-ci.org/damejidlo/envy.svg?branch=master)](https://travis-ci.org/damejidlo/envy)
[![Downloads this Month](https://img.shields.io/packagist/dm/damejidlo/envy.svg)](https://packagist.org/packages/damejidlo/envy)
[![Latest stable](https://img.shields.io/packagist/v/damejidlo/envy.svg)](https://packagist.org/packages/damejidlo/envy)


## Installation

```sh
$ composer require damejidlo/envy
```


## Basic usage

Create `Envy instance`:

```php
use Damejidlo\Envy\Envy;
use Damejidlo\Envy\LoaderFactory;
use Damejidlo\Envy\ValueProviders\Reader;

$reader = new Reader();
$loaderFactory = new LoaderFactory($reader);
$envy = new Envy($reader, $loaderFactory);
```

`Envy` supports several methods for loading the most common data types from env variables. When loading the value, it is first validated (throws exception on invalid input), and then type-casted.


### String values

```php
// Get string from `FOO` env variable, or throw exception if not set
$envy->getString('FOO');
// Get string from `FOO` env variable, or return `'default'` if not set
$envy->getString('FOO', 'default');
// Get string from `FOO` env variable, or return `NULL` if not set
$envy->getStringOrNull('FOO');
```

### Boolean values

There are multiple supported ways of expressing boolean values: `true`/`false`, `yes`/`no`, `1`/`0` (all case insensitive).

```php
// Get boolean from `FOO` env variable, or throw exception if not set
$envy->getBool('FOO');
// Get boolean from `FOO` env variable, or return `FALSE` if not set
$envy->getBool('FOO', FALSE);
// Get boolean from `FOO` env variable, or return `NULL` if not set
$envy->getBoolOrNull('FOO');
```

### Integer values

```php
// Get integer from `FOO` env variable, or throw exception if not set
$envy->getInt('FOO');
// Get integer from `FOO` env variable, or return `42` if not set
$envy->getInt('FOO', 42);
// Get integer from `FOO` env variable, or return `NULL` if not set
$envy->getIntOrNull('FOO');
```

### Float values

```php
// Get float from `FOO` env variable, or throw exception if not set
$envy->getFloat('FOO');
// Get float from `FOO` env variable, or return `3.1415` if not set
 $envy->getFloat('FOO', 3.1415);
// Get float from `FOO` env variable, or return `NULL` if not set
$envy->getFloatOrNull('FOO');
```

### Arrays

The default item delimiter `~\s*,\s*~` is used to split the items.

#### List of string values 

```php
// Get array of string from `FOO` env variable, or throw exception if not set
$envy->getStringArray('FOO');
// Get array of strings from `FOO` env variable, or return `['foo']` if not set
$envy->getStringArray('FOO', ['foo']);
// Get array of strings from `FOO` env variable, or return `NULL` if not set
$envy->getStringArrayOrNull('FOO');
```

#### List of integer values

```php
// Get array of integer from `FOO` env variable, or throw exception if not set
$envy->getIntArray('FOO');
// Get array of integers from `FOO` env variable, or return `[42]` if not set
$envy->getIntArray('FOO', [42]);
// Get array of integers from `FOO` env variable, or return `NULL` if not set
$envy->getIntArrayOrNull('FOO');
```

#### List of float values

```php
// Get array of float from `FOO` env variable, or throw exception if not set
$envy->getFloatArray('FOO');
// Get array of float from `FOO` env variable, or return `[3.1415]` if not set
$envy->getFloatArray('FOO', [3.1415]);
// Get array of float from `FOO` env variable, or return `NULL` if not set
$envy->getFloatArrayOrNull('FOO');
```


## Nette DI integration

Register the extension in your configuration, this will register the necessary services in your DI container:

```yaml
extensions:
    envy: Damejidlo\Envy\DI\EnvyExtension
```


You can now use a shorthand notation to access `Envy` methods:
```yaml
parameters:
    loadedFromEnvVariables:
        foo: @envy::getString('FOO')
        bar: @envy::getIntArray('FOO', [1, 1, 2, 3, 5, 8, 13])

services:
    -
        type: MyService
        arguments:
            foo: %loadedFromEnvVariables.foo%
            bar: %loadedFromEnvVariables.bar%
```


## Custom loaders

You can create your own loaders with completely customizable processing of loaded values, e.g.:

```php
use Damejidlo\Envy\LoaderFactory;
use Damejidlo\Envy\ValueProviders\Reader;

$reader = new Reader();
$loaderFactory = new LoaderFactory($reader);

$defaultValue = ['https://example.com'];

// Processor that will split items on semicolon and validate every item contains valid URL
$arrayProcessor = (new ArrayProcessor())
	->withDelimiter('~;~')
	->withItemValidator('url');

$loader = $loaderFactory->createLoader()
	->withAddedProcessor($arrayProcessor)
	->withValidator('array:1..') // make sure we're loading non-empty array
	->withFallback($defaultValue);

// Get non-empty list of URLs from `FOO_URLS` env variable, or `$defaultValue` if not set
$urls = $loader->get('FOO_URLS');
```

If the default set of processors does not cover your use case, you can write your own by implementing `ProcessorInterface`.


## Validation

The validation is build on top of `Nette\Utils\Validators` - you can validate against any type Nette supports.
