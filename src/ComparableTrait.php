<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core;

use Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator;
use Fleshgrinder\Core\Comparators\{
	Comparator, ComparatorDelegate, ReverseComparator
};

/**
 * Default implementation for the {@see Comparable} interface.
 *
 * It provides default implementations for all methods of the {@see Comparable}
 * interface, and it will produce a lexicographic ordering based on the
 * top-to-bottom declaration of the object’s properties. It uses the
 * {@see RecursiveArrayComparator} to do so.
 *
 * @mixin \Fleshgrinder\Core\Comparable
 * @see \Fleshgrinder\Core\Comparable
 * @see \Fleshgrinder\Core\ArrayComparators\RecursiveArrayComparator
 */
trait ComparableTrait {
	/**
	 * @see \Fleshgrinder\Core\Comparable::getComparator
	 * @return callable|\Fleshgrinder\Core\Comparators\Comparator
	 */
	public static function getComparator(): Comparator {
		\assert(
			(new \ReflectionClass(static::CLASS))->implementsInterface(Comparable::CLASS),
			static::CLASS . ' must implement ' . Comparable::CLASS
		);

		return ComparatorDelegate::new(static function ($lhs, $rhs): int {
			if (\is_a($lhs, static::CLASS)) {
				/* @noinspection PhpUndefinedMethodInspection */
				return $lhs->compareTo($rhs)->toInt();
			}

			/* @noinspection ExceptionsAnnotatingAndHandlingInspection */
			throw UncomparableException::new(
				'Expected {} but got {:?} on left-hand side',
				[static::CLASS, $lhs]
			);
		});
	}

	/**
	 * @see \Fleshgrinder\Core\Comparable::getReverseComparator
	 * @return callable|\Fleshgrinder\Core\Comparators\Comparator
	 */
	public static function getReverseComparator(): Comparator {
		return ReverseComparator::new(static::getComparator());
	}

	/**
	 * @see \Fleshgrinder\Core\Comparable::compareTo
	 * @throws \Fleshgrinder\Core\UncomparableException
	 */
	public function compareTo($other): Ordering {
		if ($other instanceof $this) {
			/* @noinspection PhpParamsInspection */
			return $this->compareTypeSafeTo($other);
		}

		throw UncomparableException::fromIncompatibleTypes($this, $other);
	}

	/** @see \Fleshgrinder\Core\Comparable::isLessThan */
	public function isLessThan($other): bool {
		try {
			return $this->compareTo($other)->isLess();
		}
		catch (UncomparableException $_) {
			return \false;
		}
	}

	/** @see \Fleshgrinder\Core\Comparable::isLessThanOrEquals */
	public function isLessThanOrEquals($other): bool {
		try {
			return $this->compareTo($other)->isLessOrEqual();
		}
		catch (UncomparableException $_) {
			return \false;
		}
	}

	/** @see \Fleshgrinder\Core\Equalable::equals */
	public function equals($other): bool {
		try {
			return $this->compareTo($other)->isEqual();
		}
		catch (UncomparableException $_) {
			return \false;
		}
	}

	/** @see \Fleshgrinder\Core\Comparable::isGreaterThanOrEquals */
	public function isGreaterThanOrEquals($other): bool {
		try {
			return $this->compareTo($other)->isGreaterOrEqual();
		}
		catch (UncomparableException $_) {
			return \false;
		}
	}

	/** @see \Fleshgrinder\Core\Comparable::isGreaterThan */
	public function isGreaterThan($other): bool {
		try {
			return $this->compareTo($other)->isGreater();
		}
		catch (UncomparableException $_) {
			return \false;
		}
	}

	/**
	 * Compare this object with the given other object for order.
	 *
	 * This hook is called in {@see compareTo} after checking that the other
	 * given value is an instance of this object.
	 *
	 * @throws \Fleshgrinder\Core\UncomparableException
	 */
	protected function compareTypeSafeTo(self $other): Ordering {
		return RecursiveArrayComparator::compare(
			\get_object_vars($this),
			\get_object_vars($other)
		);
	}
}
