<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core;

use Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator;
use Fleshgrinder\Core\Comparators\{Comparator, ComparatorDelegate, ReverseComparator};

/**
 * Default implementation for the {@see Comparable} interface which requires
 * implementers to define a single method only, the {@see doCompareTo}.
 *
 * @internal The final method modifier has no effect on the methods since this
 *     is a trait, however, it removes the methods from the _override method_
 *     completion list of IDEs and – at least – makes it impossible that
 *     subclasses of the class that uses the trait overwrite the methods.
 * @mixin \Fleshgrinder\Core\Comparable
 */
trait ComparableTrait {
	/* @noinspection PhpDocMissingThrowsInspection */
	/**
	 * @see \Fleshgrinder\Core\Comparable::getComparator
	 * @return callable|\Fleshgrinder\Core\Comparators\Comparator
	 * @throws \Fleshgrinder\Core\UncomparableException
	 */
	final public static function getComparator(): Comparator {
		$class = static::CLASS;

		// NOTE that this should actually be a compile time error of PHP and
		// not a runtime error. Hack has support for this and Java 8 allows
		// the implementation of defaults in interfaces. However, PHP has
		// no comparable feature and forces us to implement this check at
		// runtime. (There were discussions …)
		\assert(
			(new \ReflectionClass($class))->implementsInterface(Comparable::CLASS),
			"{$class} must implement " . Comparable::CLASS
		);

		/* @noinspection ExceptionsAnnotatingAndHandlingInspection */
		return new ComparatorDelegate(static function ($lhs, $rhs) use ($class): int {
			if (($lhs instanceof $class) === \false) {
				throw UncomparableException::fromUnexpectedType($class, $lhs);
			}

			/** @var Comparable $lhs */
			return $lhs->compareTo($rhs)->toInt();
		});
	}

	/**
	 * @see \Fleshgrinder\Core\Comparable::getReverseComparator
	 * @return callable|\Fleshgrinder\Core\Comparators\Comparator
	 * @throws \Fleshgrinder\Core\UncomparableException
	 */
	final public static function getReverseComparator(): Comparator {
		return new ReverseComparator(static::getComparator());
	}

	/**
	 * @see \Fleshgrinder\Core\Comparable::compareTo
	 * @return \Fleshgrinder\Core\Ordering
	 * @throws \Fleshgrinder\Core\UncomparableException
	 */
	final public function compareTo($other): Ordering {
		$ordering = $this->doCompareTo($other);

		if ($ordering instanceof NullOrdering) {
			throw UncomparableException::fromUnexpectedType(static::CLASS, $other);
		}

		return $ordering;
	}

	/** @see \Fleshgrinder\Core\Comparable::isLessThan */
	final public function isLessThan($other): bool {
		return $this->doCompareTo($other)->isLess();
	}

	/** @see \Fleshgrinder\Core\Comparable::isLessThanOrEquals */
	final public function isLessThanOrEquals($other): bool {
		return $this->doCompareTo($other)->isLessOrEqual();
	}

	/** @see \Fleshgrinder\Core\Equalable::equals */
	final public function equals($other): bool {
		return $this->doCompareTo($other)->isEqual();
	}

	/** @see \Fleshgrinder\Core\Comparable::isGreaterThanOrEquals */
	final public function isGreaterThanOrEquals($other): bool {
		return $this->doCompareTo($other)->isGreaterOrEqual();
	}

	/** @see \Fleshgrinder\Core\Comparable::isGreaterThan */
	final public function isGreaterThan($other): bool {
		return $this->doCompareTo($other)->isGreater();
	}

	/**
	 * Do compare this object with the given other value, this method **should
	 * not** throw an {@see UncomparableException} as it is used for direct
	 * chaining. A {@see NullOrdering} should be used as return value instead.
	 *
	 * Implementers must ensure that this method does not throw any errors or
	 * exceptions.
	 *
	 * The default implementation uses the {@see RecursiveArrayComparator}, and
	 * compares all properties of this and the other objects against each other,
	 * if, and only if, they are instances of the same superclass. Note that
	 * the properties cannot be nullable, and that all must be set and exist.
	 */
	protected function doCompareTo($other): Ordering {
		if ($other instanceof $this) {
			$lhs = \get_object_vars($this);
			$rhs = \get_object_vars($other);

			try {
				return RecursiveArrayComparator::compare($lhs, $rhs);
			}
			catch (UncomparableException $e) {
				// Fall through!
			}
		}

		return new NullOrdering;
	}
}
