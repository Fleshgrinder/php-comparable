<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

namespace Fleshgrinder\Core;

use Fleshgrinder\Core\Comparators\Comparator;

/**
 * The **comparable** interface provides the functionality for ordering and
 * comparison of values whose magnitude can be compared. It imposes an order
 * on the objects of each class that implement it, however, the concrete
 * implementation decides whether it is a
 * [total](https://en.wikipedia.org/wiki/Toset) or
 * [partial](https://en.wikipedia.org/wiki/Poset) order they provide. This
 * interface does not provide any guarantees in this regard.
 *
 * Implementers are encouraged to make use of the {@see ComparableTrait}, which
 * provides defaults for all methods required by this interface.
 *
 * @see \Fleshgrinder\Core\ComparableTrait
 */
interface Comparable extends Equalable {
	/**
	 * Get a {@see Comparator} for this concrete comparable instance.
	 *
	 * @return callable|\Fleshgrinder\Core\Comparators\Comparator
	 */
	static function getComparator(): Comparator;

	/**
	 * Get a reversed {@see Comparator} for this concrete comparable instance.
	 *
	 * @return callable|\Fleshgrinder\Core\Comparators\Comparator
	 */
	static function getReverseComparator(): Comparator;

	/**
	 * Compare this object with the given other value for order.
	 *
	 * Implementers **must** ensure that this method adheres to the axioms of a
	 * [poset](https://en.wikipedia.org/wiki/Poset). That is, for all 𝑎, 𝑏, and
	 * 𝑐 that are instances of this object, the following **must** be satisfied:
	 *
	 * 1. 𝑎 ≼ 𝑎
	 *    ([reflexivity](https://en.wikipedia.org/wiki/Reflexive_relation):
	 *    every element is related to itself),
	 * 2. if 𝑎 ≼ 𝑏 and 𝑏 ≼ 𝑎, then 𝑎 = 𝑏
	 *    ([antisymmetry](https://en.wikipedia.org/wiki/Antisymmetric_relation):
	 *    two distinct elements cannot be related in both directions),
	 * 3. if 𝑎 ≼ 𝑏 and 𝑏 ≼ 𝑐, then 𝑎 ≼ 𝑐
	 *    ([transitivity](https://en.wikipedia.org/wiki/Transitive_relation):
	 *    if a first element is related to a second element, and, in turn, that
	 *    element is related to a third element, then the first element is
	 *    related to the third element).
	 *
	 * Implementers **should** ensure that this method adheres to the axioms
	 * of a [toset](https://en.wikipedia.org/wiki/Toset). That is, only one of
	 * 𝑎 ≺ 𝑏, 𝑎 = 𝑏, or 𝑎 ≻ 𝑏 is true, as well as all of the axioms of a poset
	 * explained above.
	 *
	 * @throws \Fleshgrinder\Core\UncomparableException
	 *     if the given other value is not comparable with this object.
	 */
	function compareTo($other): Ordering;

	/** Determine if this object is less than the other value. */
	function isLessThan($other): bool;

	/** Determine if this object is less than or equals the other value. */
	function isLessThanOrEquals($other): bool;

	/** Determine if this object is greater than or equals the other value. */
	function isGreaterThanOrEquals($other): bool;

	/** Determine if this object is greater than the other value. */
	function isGreaterThan($other): bool;
}
