[![Latest Stable Version](https://poser.pugx.org/fleshgrinder/comparable/v/stable)](https://packagist.org/packages/fleshgrinder/comparable)
[![License](https://poser.pugx.org/fleshgrinder/comparable/license)](https://packagist.org/packages/fleshgrinder/comparable)
[![Travis CI build status](https://img.shields.io/travis/Fleshgrinder/php-comparable.svg)](https://travis-ci.org/Fleshgrinder/php-comparable)
[![AppVeyor CI build status](https://ci.appveyor.com/api/projects/status/8n4orrc7t5eec1ev/branch/master?svg=true)](https://ci.appveyor.com/project/Fleshgrinder/php-comparable/branch/master)

[![Dependency Status](https://gemnasium.com/badges/github.com/Fleshgrinder/php-comparable.svg)](https://gemnasium.com/github.com/Fleshgrinder/php-comparable)
[![Coveralls branch](https://img.shields.io/coveralls/Fleshgrinder/php-comparable/master.svg)](https://coveralls.io/github/Fleshgrinder/php-comparable)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/Fleshgrinder/php-comparable.svg)](https://scrutinizer-ci.com/g/Fleshgrinder/php-comparable/)
[![Code Climate: GPA](https://img.shields.io/codeclimate/github/Fleshgrinder/php-comparable.svg)](https://codeclimate.com/github/Fleshgrinder/php-comparable)
[![Total Downloads](https://poser.pugx.org/fleshgrinder/comparable/downloads)](https://packagist.org/packages/fleshgrinder/comparable)
# Comparable
The **comparable** library provides type-safe custom comparison for PHP objects
as well as built-in primitive types.

- [Installation](#installation)
- [Usage](#usage)
    - [Ordering](#ordering)
    - [Comparators](#comparators)
    - [Array Comparators](#array-comparators)
- [Testing](#testing)

## Installation
Open a terminal, enter your project directory and execute the following command
to add this library to your dependencies:

```bash
composer require fleshgrinder/comparable
```

This command requires you to have Composer installed globally, as explained in
the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the
Composer documentation.

## Usage
The _[Comparable](src/Comparable.php)_ interface is the basic building block of
this library. Classes that wish to provide custom comparability should implement
it, upon doing so they also automatically implement the
_[Equalable](https://github.com/Fleshgrinder/php-equalable)_ interface, since
_Comparable_ extends _Equalable_. This means that classes with custom
comparability provide out-of-the-box custom equality as well. There are a couple
of methods that implementers are required to provide:

- _[getComparator](src/Comparable.php#19-25)_ — a static method to retrieve a
  type-safe comparator for this particular class that can be used in places
  where a callable is required, e.g. _[usort](https://php.net/usort)_.
- _[getReverseComparator](src/Comparable.php#27-33)_ — a static method that
  returns a reversed version of the comparator that getComparator returns, for
  e.g. descending sorting.
- _[compareTo](src/Comparable.php#35-65) — the most important method, which
  takes a single argument against which this instance should be compared to. It
  throws an _[UncomparableException](src/UncomparableException.php)_ if the
  other value cannot be compared against this instance. An [Ordering](#ordering)
  is returned if the values are comparable.
- The various _is_-methods use _compareTo_ internally, but they do not throw an
  exception an instead always return a Boolean that indicates whether the other
  given value adheres to the constraints or not:
    - _[isLessThan](src/Comparable.php#67-68)_
    - _[isLessThanOrEquals](src/Comparable.php#70-71)_
    - _equals_ (which originates from the _Equalable_ interface)
    - _[isGreaterThanOrEquals](src/Comparable.php#73-74)_
    - _[isGreaterThan](src/Comparable.php#76-77)_

The _[ComparatorTrait](src/ComparableTrait.php)_ is provided along with the
_Comparable_ interface to provide sane default implementations for all of these
methods, because they are always the same for all implementing classes. The
trait makes all of the above methods final and adds a new one which may be
overwritten by implementers to customize the default comparison strategy, which
is as follows:

1. Check if the given other value is an instance of the implementing class, if
   not a [NullOrdering](#ordering) instance is returned, and the method ends,
   otherwise it continues with:
2. Retrieve all properties from both, this and the other instance.
3. Compare the properties with the _RecursiveArrayComparator_ (see
   [Array Comparators](#array-comparators) for more information) and return its
   result, or a _NullOrdering_ if it throws an _UncomparableException_.

This is the strictest possible implementation, which works great for value
objects. Entities or other more complex classes might require a different
strategy. It is also not a very efficient implementation, due to the recursive
nature of it. Hence, implementers are highly encouraged to overwrite the
_[doCompareTo](src/ComparableTrait.php#105-132) method, and provide an optimized
custom logic for comparison. Note well that the _doCompareTo_ method is not
allowed to throw or emit any errors or exceptions. A _NullOrdering_ must be
returned instead if the given other value is uncomparable with the instance.

#### Example
The following example makes use of the
[money pattern](https://www.martinfowler.com/eaaCatalog/money.html) to
illustrate how custom comparison can be implemented with this library, as well
as what benefits custom comparison in rich domain models has.

```php
<?php

declare(strict_types = 1);

```

### Ordering
The _[Ordering](src/Ordering.php)_ class is an enumeration that represents the
result of a comparison. It encapsulates the typical comparison result values
−1, 0, and 1 that stand for _“less than”_, _“equal”_, and _“greater than”_.
These traditional non-type-safe values are provided as class constants
_[LT](src/Ordering.php#16-17)_, _[EQ](src/Ordering.php#19-20)_, and
_[GT](src/Ordering.php#22-23). However, there are also three named constructors
(factory methods) named _[Less](src/Ordering.php#33-36),
_[Equal](src/Ordering.php#38-41)_, and _[Greater](src/Ordering.php#43-46) that
represent the exact same values, but as instances of _Ordering_, and are thus
type-safe. Note that it is also possible to construct an instance from any
integer value via the _[constructor](src/Ordering.php#28-31)_. In this case,
values less than zero are considered to be instances of _Less_, greater than
zero are _Greater_, and exactly zero are _Equal_. _Ordering_ features a couple
of useful methods:

-	__toString — returns the fully-qualified class name with the name of the corresponding named constructor appended to it (e.g. Fleshgrinder\Core\Ordering::Less).
-	There are various is-methods that return a Boolean to find out what kind of Ordering we are dealing with:
o	isLess
o	isLessOrEqual
o	isEqual
o	isGreaterOrEqual
o	isGreater
-	then — is for chaining comparisons in a functional manner, it returns the result of the next comparison if the initial comparison is not equal, e.g. $x->compareTo($y)->then($y->compareTo($z)).
-	thenWith — works exactly like then but accepts a callable, this is useful if the next comparison would be costly, since the construction of a closure is cheap. Note that the callable must return an instance of Ordering.
-	toInt — transforms the Ordering instance to its corresponding scalar integer value which is always in [−1, 1] even if it was constructed with a custom integer value.
-	toReverse — reverses the encapsulated order:
o	Less becomes Greater,
o	Equal stays Equal, and
o	Greater becomes Less.
-	toString — returns a human-readable representation of the order:
o	Less becomes “less”,
o	Equal becomes “equal”, and
o	Greater becomes “greater”.

Of course, Ordering implements Comparable and features all of the same functionality. It is also possible to compare an Ordering instance directly against scalar integer values, the same rules apply to the comparison as if the given other value was constructed from a scalar integer value.

There is also the NullOrdering class, a null object implementation for Ordering that is used in custom doCompareTo methods to avoid stack unrolling (through exceptions), and method chaining in internal logic. It works similar to Ordering, but has a few differences:

-	__toString returns its fully-qualified name: Fleshgrinder\Core\NullOrdering.
-	All is-Methods return false, always.
-	then and thenWith return or invoke the given argument, always.
-	toInt returns −2, always.
-	toReverse returns itself, always.
-	toString returns “uncomparable”.

Any value that is compared against a NullOrdering results in a NullOrdering, hence, it is uncomparable against any and every value, including itself.

### Comparators
The _[Comparator](src/Comparators/Comparator.php)_ interface is a functional
interface for custom comparators, it requires implementers to provide a single
magic _[__invoke](src/Comparators/Comparator.php#12-18)_ method that takes two
arguments and returns an integer. Custom comparators are of interest to
encapsulate a specific strategy for comparing values, like the
[Array Comparators](#array-comparators) that are part of this library. However,
most of the time it makes no sense to write a dedicated class for a custom
comparator, since a simple closure is good enough.

The _[ComparatorDelegate](src/Comparators/ComparatorDelegate.php)_ class can be
used in such cases to construct a _Comparator_ instance from such a closure,
this ensures type-safety, while still being able to use the wrapped closure in
places where a callable type constraint is in place. A nice feature of the
_ComparatorDelegate_ is that it catches
_[TypeError](https://php.net/type-error)_s and transforms them into
_UncomparableException_s.

```php
<?php

declare(strict_types = 1);

use Fleshgrinder\Core\Comparators\ComparatorDelegate;

$dates = [new \DateTimeImmutable, new \DateTimeImmutable('@0')];

\usort($dates, ComparatorDelegate::new(static function (\DateTimeImmutable $lhs, \DateTimeImmutable $rhs): int {
	return 
}));
```

Also provided is a generic ReverseComparator that takes any Comparator instances and reverses it by flipping the left-hand side argument with the right-hand side one. Besides that, there are the DefaultComparator for comparing values in a type-safe manner while honoring classes that provide custom comparability (read, classes that implement the Comparable interface), and the NullableComparator that uses the DefaultComparator internally, but before delegating to it, it checks if any of the two given arguments is null. If both arguments are null the result is considered to be equal, if the left-hand side is null the result will be less, and if the right-hand side is null the result will be greater.

### Array Comparators
The various Comparators for comparing array values provide differing degrees of type-safety, as well as two strategies to deal with n-dimensional arrays. The simplest and most performant version is the ArrayComparator. It makes use of the DefaultComparator to compare the array elements, and throws an UncomparableException if the sizes or keys mismatch. The default PHP comparison for arrays is used, in case the two given arrays contain multiple dimensions. There are three additional variations of this comparator:

1.	The NullableSparseArrayComparator that ignores size and key mismatches, and uses the NullableComparator internally to compare the array elements.
2.	The NullableArrayComparator that is strict in case of size or key mismatches, but uses the NullableComparator internally to compare the array elements.
3.	The SparseArrayComparator that ignores size and key mismatches, but not type mismatches, since it uses the DefaultComparator to compare the array elements.

The strictest and most complex version is the RecursiveArrayComparator. The difference to the ArrayComparator is that it uses the DefaultComparator for all dimensions that the given arrays might contain. It features the same three variations as the ArrayComparator: the NullableSparseRecursiveArrayComparator, NullableRecursiveArrayComparator, and the SparseRecursiveArrayComparator.

This might seem over-engineered to some, but the array data structure in PHP is simply very, very complex, and even these strategies will not cover all possible use-cases.


# ------------------------------------------------------------------------------

## Testing
Open a terminal, enter the project directory and execute `make` to run the
[PHPUnit](https://phpunit.de/) tests with your locally installed PHP executable.
