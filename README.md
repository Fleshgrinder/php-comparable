[![Packagist](https://img.shields.io/packagist/v/Fleshgrinder/comparable.svg?style=flat-square)](https://packagist.org/packages/fleshgrinder/comparable)
[![Packagist License](https://img.shields.io/packagist/l/Fleshgrinder/comparable.svg?style=flat-square)](https://packagist.org/packages/fleshgrinder/comparable)
# Comparable
Interface to implement custom comparison logic for classes instead of writing them inline over and over again.
 
Provided is the interface itself that establishes the contract that implementing classes have to have the compare
 method. Furthermore a specific exception is provided that can be used to notify callers that a comparison of the passed
 value is not possible. Last but not least a dummy class that can be used in tests as a substitute for doubles, stubs,
 or mocks is included as well.

## Installation
Open a terminal, enter your project directory and execute the following command to add this package to your
 dependencies:

```bash
$ composer require fleshgrinder/comparable
```

This command requires you to have Composer installed globally, as explained in the
 [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

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

## License
[![MIT License](https://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/License_icon-mit.svg/48px-License_icon-mit.svg.png)](https://opensource.org/licenses/MIT)
