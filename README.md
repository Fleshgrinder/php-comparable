[![Latest Stable Version](https://poser.pugx.org/fleshgrinder/comparable/v/stable)](https://packagist.org/packages/fleshgrinder/comparable)
[![License](https://poser.pugx.org/fleshgrinder/comparable/license)](https://packagist.org/packages/fleshgrinder/comparable)
[![Travis CI build status](https://img.shields.io/travis/Fleshgrinder/php-comparable.svg)](https://travis-ci.org/Fleshgrinder/php-comparable)
[![AppVeyor CI build status](https://ci.appveyor.com/api/projects/status/36pbndq2e739llp1/branch/master?svg=true)](https://ci.appveyor.com/project/Fleshgrinder/php-comparable/branch/master)

[![Dependency Status](https://gemnasium.com/badges/github.com/Fleshgrinder/php-comparable.svg)](https://gemnasium.com/github.com/Fleshgrinder/php-comparable)
[![Coveralls branch](https://img.shields.io/coveralls/Fleshgrinder/php-comparable/master.svg)](https://coveralls.io/github/Fleshgrinder/php-comparable)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/Fleshgrinder/php-comparable.svg)](https://scrutinizer-ci.com/g/Fleshgrinder/php-comparable/)
[![Code Climate: GPA](https://img.shields.io/codeclimate/github/Fleshgrinder/php-comparable.svg)](https://codeclimate.com/github/Fleshgrinder/php-comparable)
[![Total Downloads](https://poser.pugx.org/fleshgrinder/comparable/downloads)](https://packagist.org/packages/fleshgrinder/comparable)
# Comparable
The **comparable** library provides a 

Interface to implement custom comparison logic for classes instead of writing
 them inline over and over again.
 
Provided is the interface itself that establishes the contract that implementing
 classes have to have the compare method. Furthermore a specific exception is
 provided that can be used to notify callers that a comparison of the passed
 value is not possible. Last but not least a dummy class that can be used in
 tests as a substitute for doubles, stubs, or mocks is included as well.

- [Installation](#installation)
- [Usage](#usage)
- [Testing](#testing)

## Installation
Open a terminal, enter your project directory and execute the following command
 to add this package to your dependencies:

```bash
composer require fleshgrinder/comparable
```

This command requires you to have Composer installed globally, as explained in
 the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the
 Composer documentation.

## Usage
Simply implement the interface and the required `compareTo` method.

```php
class YourClass implements Comparable {

    const TOLERANCE = 0.0001;
    
    protected $value;
    
    public function __construct($value) {
        $this->value = (float) $value;
    }

    /**
     * @inheritDoc
     */
    public function compareTo($other) {
        if (($other instanceof $this) === false) {
            throw new UncomparableException();
        }
        
        $diff = $other->value() - $this->value;
        
        if ($diff > static::TOLERANCE) {
            return 1;
        }
        
        if ($diff < -static::TOLERANCE) {
            return -1;
        }
        
        return 0;
    }

}
```

## Testing
Open a terminal, enter the project directory and execute the following commands
 to run the [PHPUnit](https://phpunit.de/) tests with your locally installed
 PHP executable.

```bash
make
```

You can also execute the following two commands, in case `make` is not
 available on our system:

```bash
composer install
composer test
```
