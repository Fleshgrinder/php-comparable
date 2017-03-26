<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\Ordering;

/**
 * The **comparator trait** provides methods for the creation ({@see new}) and
 * direct invocation ({@see compare}) of comparators.
 *
 * @mixin \Fleshgrinder\Core\Comparators\Comparator
 */
trait ComparatorTrait {
	/**
	 * Compare the left- with the right-hand side.
	 *
	 * @throws \Fleshgrinder\Core\UncomparableException
	 *     if the left-hand side cannot be compared with the right-hand side.
	 */
	final public static function compare($lhs, $rhs): Ordering {
		return Ordering::new((new static)($lhs, $rhs));
	}

	/** Construct new comparator instance. */
	final public static function new(): self {
		return new static;
	}
}
