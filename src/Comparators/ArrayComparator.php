<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\{Ordering, UncomparableException, Value};

/**
 * The **array comparator** may be used to compare unidimensional arrays which
 * contain mixed data.
 *
 * @see \Fleshgrinder\Core\Comparators\ArrayRecursiveComparator
 */
final class ArrayComparator extends Comparator {
	/** @inheritDoc */
	public function __invoke($lhs, $rhs): int {
		if ($lhs === [] && $rhs === []) {
			return Ordering::EQ;
		}

		if (\is_array($lhs) === \false || \is_array($rhs) === \false) {
			/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
			throw UncomparableException::fromUnexpectedTypes(Value::TYPE_ARRAY, $lhs, $rhs);
		}

		$order = \count($lhs) <=> \count($rhs);
		if ($order !== Ordering::EQ) {
			return $order;
		}

		$cmp = new DefaultComparator;
		foreach ($lhs as $delta => $lhs_item) {
			if (\array_key_exists($delta, $rhs) === \false) {
				/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
				throw UncomparableException::againstVoid($lhs_item, ", key `{$delta}` missing from right-hand side");
			}

			$order = $cmp($lhs_item, $rhs[$delta]);
			if ($order !== Ordering::EQ) {
				return $order;
			}
		}

		return Ordering::EQ;
	}
}
