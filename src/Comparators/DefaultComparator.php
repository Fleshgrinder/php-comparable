<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\{Comparable, UncomparableException, Value};

/**
 * The default comparator uses PHP’s built-in comparison operation while
 * forwarding to the `compareTo` method if the left-hand side is a comparable.
 *
 * ## Examples
 * ```php
 * $data = [3, 2, 1];
 *
 * usort($data, new DefaultComparator);
 *
 * var_export($data);
 * // array (
 * //   0 => 1,
 * //   1 => 2,
 * //   2 => 3,
 * // )
 * ```
 */
final class DefaultComparator extends Comparator {
	/** @inheritDoc */
	public function __invoke($lhs, $rhs): int {
		if (Value::getType($lhs) !== Value::getType($rhs)) {
			/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
			throw UncomparableException::fromIncompatibleTypes($lhs, $rhs);
		}

		if ($lhs instanceof Comparable) {
			return $lhs->compareTo($rhs)->toInt();
		}

		return $lhs <=> $rhs;
	}
}
