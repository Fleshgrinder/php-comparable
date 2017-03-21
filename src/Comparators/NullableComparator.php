<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\{Ordering, Uncloneable};

/**
 * The **nullable comparator** uses the {@see DefaultComparator} to compare
 * values, however, any null value on either side will not lead to an exception;
 * as it would with the {@see DefaultComparator}. Note that every value is
 * considered to be greater than null, and that null is considered equal to
 * null.
 */
class NullableComparator implements Comparator {
	use ComparatorTrait, Uncloneable;

	/** @inheritDoc */
	public function __invoke($lhs, $rhs): int {
		if ($lhs === \null && $rhs === \null) {
			return Ordering::EQ;
		}

		if ($lhs === \null) {
			return Ordering::LT;
		}

		if ($rhs === \null) {
			return Ordering::GT;
		}

		return (new DefaultComparator)($lhs, $rhs);
	}
}
