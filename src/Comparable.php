<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

namespace Fleshgrinder\Core;

use Fleshgrinder\Core\Comparators\Comparator;

/**
 * The **comparable** interface defines the contract for all classes that
 * provide custom ordering and consequently sorting. Implementers are
 * encouraged to make use of the {@see ComparableTrait}, which comes along with
 * this interface to simplify the implementation.
 */
interface Comparable extends Equalable {
	/**
	 * Get the default comparator of this comparable instance for functions that
	 * require a callback, e.g. {@see \usort}.
	 *
	 * @return callable|\Fleshgrinder\Core\Comparators\Comparator
	 */
	static function getComparator(): Comparator;

	/**
	 * Get the reversed default comparator of this comparable instance for
	 * functions that require a callback, e.g. {@see \usort}.
	 *
	 * @return callable|\Fleshgrinder\Core\Comparators\Comparator
	 */
	static function getReverseComparator(): Comparator;

	/**
	 * Compare this object with the specified value for order.
	 *
	 * The implementor must ensure
	 * `gmp_sign($x->compareTo($y)) === -gmp_sign($y->compareTo($x))` for all
	 * `$x` and `$y`. (This implies that `$x->compareTo($y)` must throw an
	 * exception if `$y->compareTo($x)` throws an exception.)
	 *
	 * The implementor must also ensure that the relation is transitive:
	 * `$x->compareTo($y) > 0 && $y->compareTo($z) > 0` implies
	 * `$x->compareTo($z) > 0`.
	 *
	 * Finally, the implementor must ensure that `$x->compareTo($y) === 0`,
	 * which implies that
	 * `gmp_sign($x->compareTo($z)) === gmp_sign($y->compareTo($z))`, for all
	 * `$z`.
	 *
	 * It is strongly recommended, but not strictly required that
	 * `($x->compareTo($y) === 0) === $x->equals($y)`. Generally speaking,
	 * any class that implements the {@see Comparable} interface and violates
	 * this condition should clearly indicate this fact. The recommended
	 * language is "Note: this class has a natural ordering that is
	 * inconsistent with equals."
	 *
	 * Implementers must further ensure that this method does not throw any
	 * other exceptions than the annotated ones.
	 *
	 * @throws \Fleshgrinder\Core\UncomparableException
	 *     if `$other` is not comparable with this object.
	 */
	function compareTo($other): Ordering;

	/** Whether this object is less than the other value. */
	function isLessThan($other): bool;

	/** Whether this object is less than or equals the other value. */
	function isLessThanOrEquals($other): bool;

	/** Whether this object is greater than or equals the other value. */
	function isGreaterThanOrEquals($other): bool;

	/** Whether this object is greater than the other value. */
	function isGreaterThan($other): bool;
}
