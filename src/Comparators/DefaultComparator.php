<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\{
	Comparable, Formatter, Uncloneable, UncomparableException, Value
};

/**
 * The **default comparator** uses PHP’s built-in comparison operation while
 * ensuring type security and forwarding to the `compareTo` method if the
 * left-hand side is a comparable.
 *
 * ## Examples
 * ```php
 * use Fleshgrinder\Core\Comparators\DefaultComparator;
 *
 * $data = [3, 2, 1];
 *
 * usort($data, new DefaultComparator);
 *
 * assert($data === [1, 2, 3]);
 * ```
 *
 * ```php
 * use Fleshgrinder\Core\{Comparators\DefaultComparator, UncomparableException};
 *
 * $data = ['3', 2, 1.0];
 *
 * $e = null;
 * try {
 *     usort($data, new DefaultComparator);
 * }
 * catch (UncomparableException $e) { }
 *
 * assert($e instanceof UncomparableException);
 * ```
 */
final class DefaultComparator implements Comparator {
	use ComparatorTrait, Uncloneable;

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
