<?php
/**
 * @author Richard Fussenegger <fleshgrinder@users.noreply.github.com>
 * @copyright 2017 Richard Fussenegger
 * @license http://unlicense.org/ Unlicense
 */

declare(strict_types = 1);

namespace Fleshgrinder\Core\Comparators;

use Fleshgrinder\Core\{Comparable, Ordering, UncomparableException, Value};

/**
 * Compare item by item of an arbitrarily nested array or map.
 *
 * The name _recursive_ is actually misleading since the implementation does
 * not perform any recursion, however, it is common practice in PHP to name
 * array functions that support multiple dimensions _recursive_ and we adhere
 * to this tradition to indicate what this comparator is capable of.
 */
final class ArrayRecursiveComparator extends Comparator {
	/** @inheritDoc */
	public function __invoke($lhs, $rhs): int {
		if (\is_array($lhs) === \false || \is_array($rhs) === \false) {
			/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
			throw UncomparableException::fromUnexpectedTypes(Value::TYPE_ARRAY, $lhs, $rhs);
		}

		$comparator = new DefaultComparator;
		$l_stack    = [$lhs];
		$r_stack    = [$rhs];

		for ($ptr = 0; $ptr > -1;) {
			$delta   = \key($l_stack[$ptr]);
			$l_value = $l_stack[$ptr][$delta];

			if (\array_key_exists($delta, $r_stack[$ptr]) === \false) {
				/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
				throw UncomparableException::againstVoid($l_stack[$delta], ", key `{$delta}` missing from right-hand side");
			}

			$r_value = $r_stack[$ptr][$delta];

			if (\is_array($l_value)) {
				if (\is_array($r_value) === \false) {
					/** @noinspection ExceptionsAnnotatingAndHandlingInspection */
					throw UncomparableException::fromUnexpectedType(Value::TYPE_ARRAY, $r_value);
				}

				if ($l_value === [] && $r_value === []) {
					goto pop_stack;
				}

				$order = \count($l_value) <=> \count($r_value);
				if ($order !== Ordering::EQ) {
					return $order;
				}

				$l_stack[] =& $l_stack[$ptr][$delta];
				$r_stack[] =& $r_stack[$ptr][$delta];
				++$ptr;

				continue;
			}

			$order = $comparator($l_value, $r_value);
			if ($order !== Ordering::EQ) {
				return $order;
			}

			pop_stack: {
				unset($l_stack[$ptr][$delta], $r_stack[$ptr][$delta]);

				if (\count($l_stack[$ptr]) < 1) {
					unset($l_stack[$ptr], $r_stack[$ptr]);
					--$ptr;
				}
			}
		}

		return Ordering::EQ;
	}
}
