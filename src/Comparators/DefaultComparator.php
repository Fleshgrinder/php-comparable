<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\{
	Comparable, Immutable, UncomparableException, Value
};

/**
 * The **default comparator** ensures type-safety while comparing values. It
 * forwards to the {@see Comparable::compareTo} method if the left-hand side
 * is an instance of {@see Comparable}, and uses the built-in spaceship operator
 * (`<=>`) for comparisons of other values.
 */
final class DefaultComparator implements Comparator {
	use ComparatorTrait, Immutable;

	/** @inheritDoc */
	public function __invoke($lhs, $rhs): int {
		if ((\is_object($rhs) && ($lhs instanceof $rhs) === \false) || Value::getType($lhs) !== Value::getType($rhs)) {
			throw UncomparableException::fromIncompatibleTypes($lhs, $rhs);
		}

		if ($lhs instanceof Comparable) {
			return $lhs->compareTo($rhs)->toInt();
		}

		return $lhs <=> $rhs;
	}
}
